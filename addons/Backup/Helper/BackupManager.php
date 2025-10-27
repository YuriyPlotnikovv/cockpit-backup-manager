<?php

namespace Backup\Helper;

use Lime\Helper;
use PharData;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class BackupManager extends Helper
{
    public function createBackup(string $backupFile, array $settings): bool
    {
        $projectRoot = dirname($this->app->path('#root:'));
        $cockpitRoot = $this->app->path('#root:');
        $backupDir = dirname($backupFile);
        $activePaths = array_filter($settings['paths'], static fn($path) => $path['active']);

        if (empty($activePaths)) {
            throw new \Exception(t('No parts selected for inclusion in the backup.'));
        }

        $backupTempDir = $this->createTempDir();
        $tempSourceDir = $this->normalizePath($backupTempDir . DIRECTORY_SEPARATOR . 'source', false);
        $copiedPaths = [];

        try {
            $this->createBackupManifest($tempSourceDir);
            $exclusions = $this->prepareExclusions(
                $projectRoot,
                $backupDir,
                $settings['exclusions'],
                $backupTempDir
            );

            foreach ($activePaths as $part) {
                $this->copyBackupPartToTemp(
                    $tempSourceDir,
                    $part,
                    $projectRoot,
                    $cockpitRoot,
                    $exclusions,
                    $copiedPaths,
                    $backupTempDir
                );
            }

            $this->createTarGzArchive($backupFile, $tempSourceDir);

            return true;
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->app->helper('fs')->delete($backupTempDir);
        }
    }

    public function restoreBackup(string $filename, string $backupDir): bool
    {
        $cockpitRoot = $this->app->path('#root:');
        $projectRoot = $_SERVER['DOCUMENT_ROOT'];
        $archivePath = $this->normalizePath($backupDir . DIRECTORY_SEPARATOR . basename($filename), false);
        $tempExtractDir = $this->createTempDir();

        try {
            $extractedSourceDir = $this->extractArchive($archivePath, $tempExtractDir);
            $manifest = $this->readBackupManifest($extractedSourceDir);
            $dbType = $manifest['database']['type'] ?? null;
            $dbDsn = $manifest['database']['dsn'] ?? null;
            $dbName = $manifest['database']['db_name'] ?? null;
            $dbDumpRelativePath = $manifest['paths']['database_dump_relative_path'] ?? 'database_dump';
            $extractedCockpitRoot = $this->normalizePath($extractedSourceDir . DIRECTORY_SEPARATOR . 'cockpit', false);
            $extractedProjectRootFiles = $this->normalizePath($extractedSourceDir, false);
            $this->restoreCockpitFiles($extractedCockpitRoot, $this->normalizePath($cockpitRoot, false));
            $this->restoreProjectRootFiles($extractedProjectRootFiles, $projectRoot, $cockpitRoot, $dbDumpRelativePath);
            $extractedDbDumpPath = $this->normalizePath($extractedSourceDir . DIRECTORY_SEPARATOR . $dbDumpRelativePath, false);

            if (is_dir($extractedDbDumpPath)) {
                if ($dbType === 'mongodb') {
                    if (empty($dbDsn) || empty($dbName)) {
                        throw new \Exception(t('Database DSN or name not found in manifest. Cannot restore MongoDB.'));
                    }

                    $dbArchiveFile = $extractedDbDumpPath . DIRECTORY_SEPARATOR . 'db.archive';
                    $this->restoreMongoDB($dbArchiveFile, $dbDsn, $dbName);
                } else if ($dbType !== 'mongolite') {
                    throw new \Exception(sprintf(t('Unsupported database type in manifest for separate restore: %s'), $dbType));
                }
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception(sprintf(t('Failed to restore backup: %s'), $e->getMessage()));
        } finally {
            if ($tempExtractDir && is_dir($tempExtractDir)) {
                $this->app->helper('fs')->delete($tempExtractDir);
            }

            $this->app->helper('system')->flushCache();
        }
    }

    public function normalizePath(string $path, bool $trailingSlash = true): string
    {
        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        if ($trailingSlash) {
            return rtrim($normalizedPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        return rtrim($normalizedPath, DIRECTORY_SEPARATOR);
    }

    public function getMongoToolBinary(string $toolName, ?string $configuredPath = null): ?string
    {
        if (!function_exists('shell_exec')) {
            throw new \Exception(t('shell_exec function is disabled. MongoDB operations for %s are not possible.', $toolName));
        }

        if ($configuredPath) {
            $normalizedToolPath = $this->normalizePath($configuredPath, false);

            if (file_exists($normalizedToolPath) && is_executable($normalizedToolPath)) {
                return '"' . $normalizedToolPath . '"';
            }
        }

        $systemEncoding = 'UTF-8';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $chcpOutput = @shell_exec('chcp 2>&1');

            if (preg_match('/Active code page:\s*(\d+)/', $chcpOutput, $matches)) {
                $codePage = $matches[1];
                if ($codePage === '866') {
                    $systemEncoding = 'CP866';
                } elseif ($codePage === '1251') {
                    $systemEncoding = 'CP1251';
                }
            } else {
                $systemEncoding = 'CP866';
            }
        }

        $rawPathOutput = shell_exec('where ' . $toolName . ' 2>&1') ?: shell_exec('which ' . $toolName . ' 2>&1');

        if ($rawPathOutput === null) {
            return null;
        }

        $pathOutput = mb_convert_encoding($rawPathOutput, 'UTF-8', $systemEncoding);

        if (
            str_contains(strtolower($pathOutput), 'not found') ||
            str_contains(strtolower($pathOutput), 'error') ||
            str_contains(strtolower($pathOutput), 'could not find')
        ) {
            return null;
        }

        $foundPaths = array_filter(array_map('trim', explode("\n", $pathOutput)));

        foreach ($foundPaths as $foundPath) {
            $normalizedFoundPath = $this->normalizePath($foundPath, false);

            if (file_exists($normalizedFoundPath) && is_executable($normalizedFoundPath)) {
                return '"' . $normalizedFoundPath . '"';
            }
        }

        return null;
    }

    protected function createTempDir(): string
    {
        $baseTempPath = $this->normalizePath($this->app->path('#tmp:'), false);
        $tempDir = $baseTempPath . DIRECTORY_SEPARATOR . uniqid('cockpit_backup_', true);

        if (!$this->app->helper('fs')->mkdir($tempDir)) {
            throw new \Exception(sprintf(t('Failed to create temporary backup directory: %s'), $tempDir));
        }

        $tempSourceDir = $tempDir . DIRECTORY_SEPARATOR . 'source';

        if (!$this->app->helper('fs')->mkdir($tempSourceDir)) {
            $this->app->helper('fs')->delete($tempDir);
            throw new \Exception(sprintf(t('Failed to create temporary source subdirectory: %s'), $tempSourceDir));
        }

        return $tempDir;
    }

    protected function prepareExclusions($projectRoot, $backupDir, $customExclusions = [], ?string $currentBackupTempDir = null): array
    {
        $cockpitRoot = $this->app->path('#root:');
        $cockpitStoragePath = $this->normalizePath($this->app->path('#storage:'), false);
        $cockpitTmpPath = $this->normalizePath($this->app->path('#tmp:'), false);
        $standardExclusions = [
            $cockpitTmpPath,
            $cockpitStoragePath . DIRECTORY_SEPARATOR . 'cache',
            $this->normalizePath($backupDir, false),
        ];

        if ($this->app->dataStorage->type === 'mongodb') {
            $standardExclusions[] = $cockpitStoragePath . DIRECTORY_SEPARATOR . 'data';
        }

        $finalExclusions = array_unique(array_merge($standardExclusions, $customExclusions));
        $absoluteExclusions = [];

        foreach ($finalExclusions as $exclusion) {
            $resolvedPath = $this->resolveExclusionPath($exclusion, $projectRoot, $cockpitRoot, $cockpitStoragePath);

            if ($resolvedPath) {
                $absoluteExclusions[] = strtolower($resolvedPath);
            }
        }

        if ($currentBackupTempDir) {
            $absoluteExclusions[] = strtolower($this->normalizePath($currentBackupTempDir, false));
        }

        return array_unique(array_filter($absoluteExclusions));
    }

    protected function copyRecursive(string $sourcePath, string $destinationPath, array $exclusions, array &$copiedPaths, ?string $currentBackupTempDir = null): bool
    {
        $realSourcePath = realpath($sourcePath);

        if (!$realSourcePath) {
            throw new \Exception(sprintf(t('Source path not found or not accessible for recursive copy: %s'), $sourcePath));
        }

        $sourcePath = $realSourcePath;

        if (empty($destinationPath)) {
            throw new \Exception(sprintf(t('Internal error: Destination path for copy operation cannot be empty. Source: %s'), $sourcePath));
        }

        if ($this->isPathExcluded($sourcePath, $exclusions, $currentBackupTempDir)) {
            if (!in_array($sourcePath, $copiedPaths, true)) {
                $copiedPaths[] = $sourcePath;
            }

            return true;
        }

        if (in_array($sourcePath, $copiedPaths, true)) {
            return true;
        }

        $copiedPaths[] = $sourcePath;
        $nativeDestinationPath = $this->normalizePath($destinationPath, false);

        if (is_dir($sourcePath)) {
            if (!$this->app->helper('fs')->mkdir($nativeDestinationPath)) {
                throw new \Exception(sprintf(t('Failed to create destination directory "%s". Source: %s'), $nativeDestinationPath, $sourcePath));
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                $filePath = $file->getRealPath();

                if (!$filePath) {
                    continue;
                }

                if ($this->isPathExcluded($filePath, $exclusions, $currentBackupTempDir)) {
                    if (!in_array($filePath, $copiedPaths, true)) {
                        $copiedPaths[] = $filePath;
                    }

                    continue;
                }

                if (in_array($filePath, $copiedPaths, true)) {
                    continue;
                }

                $copiedPaths[] = $filePath;
                $relativePath = substr($filePath, strlen($sourcePath) + 1);
                $fileDestination = $this->normalizePath($destinationPath . DIRECTORY_SEPARATOR . $relativePath, false);

                if (empty($fileDestination)) {
                    throw new \Exception(sprintf(t('Internal error: File destination path cannot be empty for file: %s'), $filePath));
                }

                if ($file->isDir()) {
                    if (!$this->app->helper('fs')->mkdir($fileDestination)) {
                        throw new \Exception(sprintf(t('Failed to create sub-directory "%s". Source: %s'), $fileDestination, $filePath));
                    }
                } elseif ($file->isFile()) {
                    if (!$this->copyRecursiveSafe($filePath, $fileDestination, false)) {
                        throw new \Exception(sprintf(t('Failed to copy file: %s'), $filePath));
                    }
                }
            }

            return true;
        }

        if (is_file($sourcePath)) {
            if (!$this->copyRecursiveSafe($sourcePath, $nativeDestinationPath, false)) {
                throw new \Exception(sprintf(t('Failed to copy single file: %s'), $sourcePath));
            }

            return true;
        }

        throw new \Exception(sprintf(t('Unsupported source type for copyRecursive: %s'), $sourcePath));
    }

    protected function createTarGzArchive(string $backupFile, string $sourceDir): bool
    {
        $nativeBackupFile = $this->normalizePath($backupFile, false);
        $nativeSourceDir = $this->normalizePath($sourceDir, false);

        if (!is_dir($nativeSourceDir)) {
            throw new \Exception(sprintf(t('Source directory for archiving not found: %s'), $nativeSourceDir));
        }

        $tmpTarFile = $nativeBackupFile . '.tmp.tar';
        $tmpTarGzFile = $nativeBackupFile . '.tmp.tar.gz';

        try {
            $phar = new PharData($tmpTarFile);
            $phar->buildFromIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($nativeSourceDir, FilesystemIterator::SKIP_DOTS)
                ),
                $nativeSourceDir
            );
            $phar->compress(\Phar::GZ);

            if (file_exists($tmpTarFile)) {
                unlink($tmpTarFile);
            }

            rename($tmpTarGzFile, $nativeBackupFile);

            if (!file_exists($nativeBackupFile)) {
                throw new \Exception(sprintf(t('Failed to create tar.gz archive: %s'), $nativeBackupFile));
            }

            return true;
        } catch (\Exception $e) {
            if (file_exists($tmpTarFile)) {
                unlink($tmpTarFile);
            }

            if (file_exists($tmpTarGzFile)) {
                unlink($tmpTarGzFile);
            }

            throw new \Exception(sprintf(t('Failed to create tar.gz archive: %s'), $e->getMessage()));
        }
    }

    protected function createBackupManifest(string $tempSourceDir): array
    {
        $defaultDsn = 'mongolite://' . $this->normalizePath($this->app->path('#storage:'), false) . DIRECTORY_SEPARATOR . 'data';
        $dsn = (string)$this->app->retrieve('database/server', $this->app->retrieve('datastore/server', $defaultDsn));
        $type = $this->app->dataStorage->type;
        $manifest = [
            'version' => '1.0',
            'timestamp' => time(),
            'cockpit_version' => APP_VERSION,
            'database' => [
                'type' => $type,
                'dsn' => null,
                'db_name' => '',
            ],
            'paths' => [
                'project_root_relative_path' => null,
                'cockpit_root_relative_path' => 'cockpit',
                'database_dump_relative_path' => 'database_dump',
            ],
            'backup_tool_version' => 'Cockpit Backup Module v1.0',
        ];

        if ($type === 'mongodb') {
            $dbConfig = $this->app->retrieve('database');
            $dbOptions = is_array($dbConfig) ? ($dbConfig['options'] ?? []) : [];
            $dbName = trim($dbOptions['db'] ?? 'cockpitdb', '/');
            $manifest['database']['dsn'] = $dsn;
            $manifest['database']['db_name'] = $dbName;
        }

        $manifestFilePath = $this->normalizePath($tempSourceDir . DIRECTORY_SEPARATOR . 'backup_manifest.json', false);

        if (!file_put_contents($manifestFilePath, json_encode($manifest, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
            throw new \Exception(sprintf(t('Failed to write backup manifest file to %s'), $manifestFilePath));
        }

        return $manifest;
    }

    protected function copyBackupPartToTemp(
        string  $tempSourceDir,
        array   $part,
        string  $projectRoot,
        string  $cockpitRoot,
        array   $exclusions,
        array   &$copiedPaths,
        ?string $currentBackupTempDir = null
    ): void
    {
        if ($part['code'] === 'Database') {
            $this->backupMongoDB($tempSourceDir);
        } elseif ($part['code'] === 'Core') {
            $this->copyCorePart($tempSourceDir, $cockpitRoot, $exclusions, $copiedPaths, $currentBackupTempDir);
        } elseif ($part['code'] === 'Site') {
            $this->copySitePart($tempSourceDir, $projectRoot, $cockpitRoot, $exclusions, $copiedPaths, $currentBackupTempDir);
        } else {
            throw new \Exception(sprintf(t('Unsupported backup part code: %s'), $part['code']));
        }
    }

    protected function copyRecursiveSafe(string $source, string $destination, bool $recursive = true): bool
    {
        $nativeSource = $this->normalizePath($source, false);
        $nativeDestination = $this->normalizePath($destination, false);

        if (!file_exists($nativeSource)) {
            throw new \Exception(sprintf(t('Source path not found for copy operation: %s'), $nativeSource));
        }

        if (is_file($nativeSource)) {
            $destDir = dirname($nativeDestination);

            if (!is_dir($destDir) && !$this->app->helper('fs')->mkdir($destDir)) {
                throw new \Exception(sprintf(t('Failed to create destination directory "%s" for file "%s".'), $destDir, $nativeDestination));
            }

            if (!@\copy($nativeSource, $nativeDestination)) {
                $error = error_get_last();
                throw new \Exception(sprintf(t('Failed to copy file from "%s" to "%s". PHP Error: %s'), $nativeSource, $nativeDestination, $error['message'] ?? 'Unknown error'));
            }

            return true;
        }

        if (is_dir($nativeSource) && $recursive) {
            if (!$this->app->helper('fs')->mkdir($nativeDestination)) {
                throw new \Exception(sprintf(t('Failed to create destination directory "%s" for source directory "%s".'), $nativeDestination, $nativeSource));
            }

            $items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($nativeSource, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($items as $item) {
                $subPathName = $items->getSubPathName();
                $subSource = $nativeSource . DIRECTORY_SEPARATOR . $subPathName;
                $subDestination = $nativeDestination . DIRECTORY_SEPARATOR . $subPathName;

                if ($item->isDir()) {
                    if (!$this->app->helper('fs')->mkdir($subDestination)) {
                        throw new \Exception(sprintf(t('Failed to create subdirectory "%s" during recursive copy.'), $subDestination));
                    }
                } elseif ($item->isFile()) {
                    if (!@\copy($subSource, $subDestination)) {
                        $error = error_get_last();
                        throw new \Exception(sprintf(t('Failed to copy sub-file from "%s" to "%s". PHP Error: %s'), $subSource, $subDestination, $error['message'] ?? 'Unknown error'));
                    }
                }
            }

            return true;
        }

        throw new \Exception(sprintf(t('Unsupported source type for copyRecursiveSafe: %s'), $nativeSource));
    }

    protected function isPathExcluded(string $filePath, array $exclusions, ?string $currentBackupTempDir = null): bool
    {
        $normalizedFilePath = strtolower($this->normalizePath($filePath, false));

        if ($currentBackupTempDir) {
            $normalizedCurrentBackupTempDir = strtolower($this->normalizePath($currentBackupTempDir, false));

            if ($normalizedFilePath === $normalizedCurrentBackupTempDir || str_starts_with($normalizedFilePath, $normalizedCurrentBackupTempDir . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        foreach ($exclusions as $ex) {
            $normalizedExclusion = strtolower($this->normalizePath($ex, false));

            if ($normalizedFilePath === $normalizedExclusion || str_starts_with($normalizedFilePath, $normalizedExclusion . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveExclusionPath(string $exclusion, string $projectRoot, string $cockpitRoot, string $cockpitStoragePath): ?string
    {
        $sanitized = trim(str_replace(['\\', '/'], '/', $exclusion), " \t\n\r\0\x0B/");

        if (empty($sanitized)) {
            return null;
        }

        if ($this->app->isAbsolutePath($sanitized)) {
            return $this->normalizePath($sanitized, false);
        }

        if (str_starts_with($sanitized, 'cockpit/')) {
            $relativeToCockpit = preg_replace('/^cockpit\//i', '', $sanitized);
            return $this->normalizePath($cockpitRoot . '/' . $relativeToCockpit, false);
        }

        if (str_starts_with($sanitized, 'storage/')) {
            $relativeToStorage = preg_replace('/^storage\//i', '', $sanitized);
            return $this->normalizePath($cockpitStoragePath . '/' . $relativeToStorage, false);
        }

        return $this->normalizePath($projectRoot . '/' . $sanitized, false);
    }

    protected function extractArchive(string $archivePath, string $destinationDir): string
    {
        $nativeFile = $this->normalizePath($archivePath, false);
        $nativeTempExtractDir = $this->normalizePath($destinationDir, false);
        $nativeTempSourceDir = $nativeTempExtractDir . DIRECTORY_SEPARATOR . 'source';

        if (!file_exists($nativeFile)) {
            throw new \Exception(sprintf(t('The backup file was not found: %s'), $archivePath));
        }

        $phar = new PharData($nativeFile);
        $phar->extractTo($nativeTempSourceDir, null, true);

        return $nativeTempSourceDir;
    }

    protected function readBackupManifest(string $extractedSourceDir): array
    {
        $manifestFilePath = $this->normalizePath($extractedSourceDir . DIRECTORY_SEPARATOR . 'backup_manifest.json', false);

        if (!file_exists($manifestFilePath)) {
            throw new \Exception(sprintf(t('Backup manifest file not found in the archive. Path: %s'), $manifestFilePath));
        }

        return json_decode(file_get_contents($manifestFilePath), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function getMongoDBConnectionDetails(): array
    {
        $dbConfig = $this->app->retrieve('database');
        $dsn = $dbConfig['server'] ?? null;

        if (empty($dsn)) {
            $dsn = $this->app->retrieve('datastore/server', 'mongodb://localhost:27017');
        }

        $dbOptions = is_array($dbConfig) ? ($dbConfig['options'] ?? []) : [];
        $dbName = trim($dbOptions['db'] ?? 'cockpitdb', '/');

        if (empty($dsn) || empty($dbName)) {
            throw new \Exception(t('MongoDB DSN or database name not configured for backup.'));
        }

        return ['dsn' => $dsn, 'db_name' => $dbName];
    }

    protected function executeMongoDumpCommand(string $dsn, string $dbName, string $dumpFile): void
    {
        $settings = $this->app->module('backup')->getSettings();
        $mongodumpPath = $this->getMongoToolBinary('mongodump', $settings['mongodumpPath'] ?? null);

        if (!$mongodumpPath) {
            throw new \Exception(t('MongoDB dump utility is not available or configured.'));
        }

        $command = sprintf(
            '%s --uri="%s" --db="%s" --archive="%s" --gzip --quiet',
            $mongodumpPath,
            escapeshellarg($dsn),
            escapeshellarg($dbName),
            escapeshellarg($dumpFile)
        );
        $output = shell_exec($command . ' 2>&1');

        if (!file_exists($dumpFile) || filesize($dumpFile) === 0) {
            $errorMessage = t('Failed to create MongoDB backup. Please ensure mongodump utility is installed and accessible.');

            if (!empty($output)) {
                $errorMessage .= sprintf(t(' Error output: %s'), $output);
            } else {
                $errorMessage .= t(' No output from mongodump command.');
            }

            throw new \Exception($errorMessage);
        }

        if (str_contains($output, 'Failed') || str_contains($output, 'error')) {
            throw new \Exception(sprintf(t('MongoDB backup failed. Error output: %s'), $output));
        }
    }

    protected function backupMongoDB(string $backupTempDir): void
    {
        if ($this->app->dataStorage->type === 'mongolite') {
            return;
        }

        if ($this->app->dataStorage->type !== 'mongodb') {
            throw new \Exception(sprintf(t('Unsupported database type for separate backup: %s'), $this->app->dataStorage->type));
        }

        $dbDetails = $this->getMongoDBConnectionDetails();
        $dbDumpDir = $this->normalizePath($backupTempDir . DIRECTORY_SEPARATOR . 'database_dump', false);

        if (!$this->app->helper('fs')->mkdir($dbDumpDir)) {
            throw new \Exception(sprintf(t('Failed to create database dump directory: %s'), $dbDumpDir));
        }

        $dumpFile = $dbDumpDir . DIRECTORY_SEPARATOR . 'db.archive';
        $this->executeMongoDumpCommand($dbDetails['dsn'], $dbDetails['db_name'], $dumpFile);
    }

    protected function copyCorePart(string $tempSourceDir, string $cockpitRoot, array $exclusions, array &$copiedPaths, ?string $currentBackupTempDir = null): void
    {
        $sourcePath = $cockpitRoot;
        $destinationPathInTemp = $this->normalizePath($tempSourceDir . DIRECTORY_SEPARATOR . 'cockpit', false);

        if (!$this->copyRecursive(
            $sourcePath,
            $destinationPathInTemp,
            $exclusions,
            $copiedPaths,
            $currentBackupTempDir
        )) {
            throw new \Exception(sprintf(t('Failed to copy Core part files. Source: %s'), $sourcePath));
        }
    }

    protected function copySitePart(string $tempSourceDir, string $projectRoot, string $cockpitRoot, array $exclusions, array &$copiedPaths, ?string $currentBackupTempDir = null): void
    {
        $sourcePath = $this->normalizePath($projectRoot, false);
        $destinationPathInTemp = $this->normalizePath($tempSourceDir, false);

        if (!is_dir($sourcePath)) {
            throw new \Exception(sprintf(t('Source directory for "Site" backup part not found: %s'), $sourcePath));
        }

        $iterator = new \DirectoryIterator($sourcePath);

        foreach ($iterator as $item) {
            if ($item->isDot()) {
                continue;
            }

            if ($item->getBasename() === basename($cockpitRoot)) {
                if (!in_array($item->getRealPath(), $copiedPaths, true)) {
                    $copiedPaths[] = $item->getRealPath();
                }

                continue;
            }

            $destForRecursive = $this->normalizePath($destinationPathInTemp . DIRECTORY_SEPARATOR . $item->getBasename(), false);

            if (!$this->copyRecursive(
                $item->getRealPath(),
                $destForRecursive,
                $exclusions,
                $copiedPaths,
                $currentBackupTempDir
            )) {
                throw new \Exception(sprintf(t('Failed to copy Site part file: %s'), $item->getRealPath()));
            }
        }
    }

    protected function restoreCockpitFiles(string $extractedCockpitRoot, string $currentCockpitRoot): void
    {
        if (!is_dir($extractedCockpitRoot)) {
            return;
        }

        $nativeCurrentCockpitRoot = $this->normalizePath($currentCockpitRoot, false);
        $nativeExtractedCockpitRoot = $this->normalizePath($extractedCockpitRoot, false);
        $runningBackupModulePath = $this->normalizePath($this->app->path('backup:'), false);
        $currentUploadsDir = $this->normalizePath($nativeCurrentCockpitRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads', false);
        $extractedUploadsDir = $this->normalizePath($nativeExtractedCockpitRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads', false);

        if (is_dir($extractedUploadsDir) || is_dir($currentUploadsDir)) {
            if (is_dir($currentUploadsDir)) {
                try {
                    $this->app->helper('fs')->delete($currentUploadsDir);
                } catch (\Exception $e) {
                    throw new \Exception(sprintf(t('Failed to clear current uploads directory "%s" before restore: %s'), $currentUploadsDir, $e->getMessage()));
                }
            }

            if (is_dir($extractedUploadsDir)) {
                if (!$this->app->helper('fs')->copy($extractedUploadsDir, $currentUploadsDir, false)) {
                    throw new \Exception(sprintf(t('Failed to copy uploads content from backup to "%s".'), $currentUploadsDir));
                }
            }
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($nativeExtractedCockpitRoot, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $itemPath = $item->getRealPath();

            if (!$itemPath) {
                continue;
            }

            $relativePath = substr($itemPath, strlen($nativeExtractedCockpitRoot) + 1);
            $targetPath = $this->normalizePath($nativeCurrentCockpitRoot . DIRECTORY_SEPARATOR . $relativePath, false);

            if (str_starts_with($targetPath, $currentUploadsDir)) {
                continue;
            }

            if (str_starts_with($targetPath, $runningBackupModulePath)) {
                continue;
            }

            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    if (!$this->app->helper('fs')->mkdir($targetPath)) {
                        throw new \Exception(sprintf(t('Failed to create directory "%s" during Cockpit restore.'), $targetPath));
                    }
                }
            } elseif ($item->isFile()) {
                if (!@\copy($itemPath, $targetPath)) {
                    $error = error_get_last();
                    throw new \Exception(sprintf(t('Failed to copy Cockpit core file from "%s" to "%s". PHP Error: %s'), $itemPath, $targetPath, $error['message'] ?? 'Unknown error'));
                }
            }
        }
    }

    protected function restoreProjectRootFiles(string $extractedProjectRootFiles, string $currentProjectRoot, string $cockpitRoot, string $dbDumpRelativePath): void
    {
        $nativeCockpitRoot = $this->normalizePath($cockpitRoot, false);
        $nativeProjectRoot = $this->normalizePath($currentProjectRoot, false);

        if ($nativeCockpitRoot === $nativeProjectRoot) {
            return;
        }

        $currentProjectItems = new \DirectoryIterator($nativeProjectRoot);

        foreach ($currentProjectItems as $item) {
            if ($item->isDot() || $item->getRealPath() === $nativeCockpitRoot) {
                continue;
            }

            try {
                $this->app->helper('fs')->delete($item->getRealPath());
            } catch (\Exception $e) {
                throw new \Exception(sprintf(t('Failed to clear current project item "%s" before restore.'), $item->getBasename()) . ' ' . $e->getMessage());
            }
        }

        $items = new \DirectoryIterator($extractedProjectRootFiles);

        foreach ($items as $item) {
            if ($item->isDot() || $item->getBasename() === 'cockpit' || $item->getBasename() === $dbDumpRelativePath || $item->getBasename() === 'backup_manifest.json') {
                continue;
            }

            $targetPath = $nativeProjectRoot . DIRECTORY_SEPARATOR . $item->getBasename();

            if (!$this->copyRecursiveSafe($item->getRealPath(), $targetPath)) {
                throw new \Exception(sprintf(t('Failed to restore project file: %s'), $item->getBasename()));
            }
        }
    }

    protected function restoreMongoDB(string $dbArchiveFile, string $dbDsn, string $dbName): void
    {
        $settings = $this->app->module('backup')->getSettings();
        $mongoshPath = $this->getMongoToolBinary('mongosh', $settings['mongoshPath'] ?? null);
        if (!$mongoshPath) {
            throw new \Exception(t('MongoDB shell utility (mongosh) is not available or configured, which is required for dropping the database before restore. Please specify the absolute path in the settings.'));
        }

        $mongorestorePath = $this->getMongoToolBinary('mongorestore', $settings['mongorestorePath'] ?? null);

        if (!$mongorestorePath) {
            throw new \Exception(t('MongoDB restore utilities (mongorestore) is not available or configured. Skipping database restoration.'));
        }

        $nativeDbArchiveFile = $this->normalizePath($dbArchiveFile, false);

        if (!file_exists($nativeDbArchiveFile)) {
            throw new \Exception(t('MongoDB archive file not found in backup for restoration.'));
        }

        $connectionUri = rtrim($dbDsn, '/') . '/' . $dbName;

        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $dropDbCommandString = sprintf(
            '%s %s --eval "db.dropDatabase()" --quiet',
            $mongoshPath,
            escapeshellarg($connectionUri)
        );

        $process = proc_open($dropDbCommandString, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            throw new \Exception(t('Failed to open process for mongosh command.'));
        }

        fclose($pipes[0]);
        $dropDbOutput = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $dropDbErrorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnValue = proc_close($process);
        $fullOutput = $dropDbOutput . $dropDbErrorOutput;

        if ($returnValue !== 0 || str_contains($fullOutput, 'Failed') || str_contains($fullOutput, 'error')) {
            throw new \Exception(sprintf(t('MongoDB database drop failed before restore. Output: %s'), $fullOutput));
        }

        $command = sprintf(
            '%s --uri="%s" --archive="%s" --nsInclude="%s.*" --gzip --quiet',
            $mongorestorePath,
            escapeshellarg($dbDsn),
            escapeshellarg($nativeDbArchiveFile),
            escapeshellarg($dbName)
        );
        $output = shell_exec($command . ' 2>&1');

        if (str_contains($output, 'Failed') || str_contains($output, 'error')) {
            throw new \Exception(sprintf(t('MongoDB restore failed. Error output: %s'), $output));
        }
    }
}
