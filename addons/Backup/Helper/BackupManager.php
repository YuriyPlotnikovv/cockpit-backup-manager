<?php

namespace Backup\Helper;

use Lime\Helper;
use PharData;

class BackupManager extends Helper
{
    public function __construct($app)
    {
        parent::__construct($app);
        if (!class_exists('PharData')) {
            throw new \Exception(t('PHP Phar extension is not enabled. It is required for backup/restore operations.'));
        }
    }

    public function removeRecursive(string $path): bool
    {
        $nativePath = str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (!file_exists($nativePath)) {
            return true;
        }

        if (is_file($nativePath)) {
            if (!@unlink($nativePath)) {
                $error = error_get_last();
                throw new \Exception(sprintf(t('Failed to delete file "%s". PHP Error: %s'), $nativePath, $error['message'] ?? 'Unknown error'));
            }
            return true;
        }

        if (is_dir($nativePath)) {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($nativePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($items as $item) {
                if ($item->isDir()) {
                    if (!@rmdir($item->getRealPath())) {
                        $error = error_get_last();
                        throw new \Exception(sprintf(t('Failed to delete subdirectory "%s". PHP Error: %s'), $item->getRealPath(), $error['message'] ?? 'Unknown error'));
                    }
                } else {
                    if (!@unlink($item->getRealPath())) {
                        $error = error_get_last();
                        throw new \Exception(sprintf(t('Failed to delete file "%s". PHP Error: %s'), $item->getRealPath(), $error['message'] ?? 'Unknown error'));
                    }
                }
            }
            if (!@rmdir($nativePath)) {
                $error = error_get_last();
                throw new \Exception(sprintf(t('Failed to delete directory "%s". PHP Error: %s'), $nativePath, $error['message'] ?? 'Unknown error'));
            }
            return true;
        }
        throw new \Exception(sprintf(t('Unknown file system item type at path: %s'), $nativePath));
    }

    public function prepareExclusions($projectRoot, $backupDir, $customExclusions = [], ?string $currentBackupTempDir = null)
    {
        $cockpitRoot = $this->app->path('#root:');
        $cockpitStoragePath = rtrim(str_replace('\\', '/', $this->app->path('#storage:')), '/');
        $cockpitTmpPath = rtrim(str_replace('\\', '/', $this->app->path('#tmp:')), '/');
        $cockpitStorageDataPath = $cockpitStoragePath . '/data';

        $standardExclusions = [
            $cockpitTmpPath,
            $cockpitStoragePath . '/cache',
            $backupDir,
        ];

        if ($this->app->dataStorage->type === 'mongodb') {
            $standardExclusions[] = $cockpitStorageDataPath;
        }

        $finalExclusions = array_unique(array_merge($standardExclusions, $customExclusions));
        $absoluteExclusions = [];

        foreach ($finalExclusions as $exclusion) {
            $sanitized = trim(str_replace('\\', '/', $exclusion), " \t\n\r\0\x0B/");
            if (empty($sanitized)) {
                continue;
            }

            $exclusionPath = '';
            if ($this->app->isAbsolutePath($sanitized)) {
                $exclusionPath = $sanitized;
            } elseif (str_starts_with($sanitized, 'cockpit/')) {
                $relativeToCockpit = preg_replace('/^cockpit\//i', '', $sanitized);
                $exclusionPath = rtrim(str_replace('\\', '/', $cockpitRoot), '/') . '/' . $relativeToCockpit;
            } elseif (str_starts_with($sanitized, 'storage/')) {
                $relativeToStorage = preg_replace('/^storage\//i', '', $sanitized);
                $exclusionPath = rtrim(str_replace('\\', '/', $cockpitStoragePath), '/') . '/' . $relativeToStorage;
            } else {
                $exclusionPath = rtrim(str_replace('\\', '/', $projectRoot), '/') . '/' . $sanitized;
            }

            if ($exclusionPath) {
                $absoluteExclusions[] = strtolower(rtrim($exclusionPath, '/'));
            }
        }

        if ($currentBackupTempDir) {
            $absoluteExclusions[] = strtolower(rtrim(str_replace('\\', '/', $currentBackupTempDir), '/'));
        }

        $absoluteExclusions = array_filter($absoluteExclusions);
        return array_unique($absoluteExclusions);
    }

    public function createTarGzArchive($backupFile, $sourceDir)
    {
        $nativeBackupFile = str_replace('/', DIRECTORY_SEPARATOR, $backupFile);
        $nativeSourceDir = str_replace('/', DIRECTORY_SEPARATOR, $sourceDir);

        if (!is_dir($nativeSourceDir)) {
            throw new \Exception(sprintf(t('Source directory for archiving not found: %s'), $nativeSourceDir));
        }

        $isEmpty = true;
        $iterator = new \FilesystemIterator($nativeSourceDir, \FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $file) {
            if ($file->getFilename() !== 'backup_manifest.json') {
                $isEmpty = false;
                break;
            }
        }

        if ($isEmpty) {
            throw new \Exception(sprintf(t('No files found to archive in source directory: %s'), $nativeSourceDir));
        }

        try {
            $phar = new PharData($nativeBackupFile . '.tmp.tar');
            $phar->buildFromIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($nativeSourceDir, \FilesystemIterator::SKIP_DOTS)
                ),
                $nativeSourceDir
            );
            $phar->compress(\Phar::GZ);

            if (file_exists($nativeBackupFile . '.tmp.tar')) {
                unlink($nativeBackupFile . '.tmp.tar');
            }
            rename($nativeBackupFile . '.tmp.tar.gz', $nativeBackupFile);

            if (!file_exists($nativeBackupFile)) {
                throw new \Exception(sprintf(t('Failed to create tar.gz archive: %s'), $nativeBackupFile));
            }
            return true;

        } catch (\Exception $e) {
            if (file_exists($nativeBackupFile . '.tmp.tar')) {
                unlink($nativeBackupFile . '.tmp.tar');
            }
            if (file_exists($nativeBackupFile . '.tmp.tar.gz')) {
                unlink($nativeBackupFile . '.tmp.tar.gz');
            }
            throw new \Exception(sprintf(t('Failed to create tar.gz archive: %s'), $e->getMessage()));
        }
    }

    public function createBackupManifest($tempSourceDir)
    {
        $defaultDsn = 'mongolite://' . rtrim($this->app->path('#storage:'), '/') . '/data';
        $dsn = (string)$this->app->retrieve('database/server', $this->app->retrieve('datastore/server', $defaultDsn));
        $type = $this->app->dataStorage->type;;

        $manifest = [
            'version' => '1.0',
            'timestamp' => time(),
            'cockpit_version' => APP_VERSION,
            'database' => [
                'type' => $type,
                'dsn' => $type === 'mongodb' ? $dsn : null,
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
            $manifest['database']['db_name'] = $dbName;
        }

        $manifestFilePath = $tempSourceDir . '/backup_manifest.json';
        $nativeManifestFilePath = str_replace('/', DIRECTORY_SEPARATOR, $manifestFilePath);

        if (!file_put_contents($nativeManifestFilePath, json_encode($manifest, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
            throw new \Exception(sprintf(t('Failed to write backup manifest file to %s'), $nativeManifestFilePath));
        }

        return $manifest;
    }

    public function copyBackupPartToTemp(
        $tempSourceDir,
        $part,
        $projectRoot,
        $cockpitRoot,
        $exclusions,
        &$addedPaths,
        ?string $currentBackupTempDir = null
    )
    {
        if ($part['code'] === 'Database') {
            if ($this->app->dataStorage->type === 'mongodb') {
                $dbBackupPath = $this->backupDatabase($tempSourceDir);
                if ($dbBackupPath) {
                    $nativeDbBackupPath = str_replace('/', DIRECTORY_SEPARATOR, $dbBackupPath);
                    if (!file_exists($nativeDbBackupPath) && !is_dir($nativeDbBackupPath)) {
                        throw new \Exception(sprintf(t('Database dump was not created successfully. Path: %s'), $nativeDbBackupPath));
                    }
                }
            } else {
                $this->app->trigger('backup.info', ['message' => t('Mongolite database is included in the "Core" backup. No separate database dump is created.')]);
            }
            return $addedPaths;
        }

        if ($part['code'] === 'Core') {
            $sourcePath = $cockpitRoot;
            $targetInArchive = 'cockpit';
        } elseif ($part['code'] === 'Site') {
            $sourcePath = $projectRoot;
            $targetInArchive = '';
        } else {
            throw new \Exception(sprintf(t('Unsupported backup part code: %s'), $part['code']));
        }

        $destinationPathInTemp = rtrim($tempSourceDir, '/');
        if (!empty($targetInArchive)) {
            $destinationPathInTemp .= '/' . $targetInArchive;
        }

        if ($part['code'] === 'Site') {
            $nativeSourcePath = str_replace('/', DIRECTORY_SEPARATOR, $sourcePath);
            if (!is_dir($nativeSourcePath)) {
                throw new \Exception(sprintf(t('Source directory for "Site" backup part not found: %s'), $nativeSourcePath));
            }
            $iterator = new \DirectoryIterator($nativeSourcePath);
            foreach ($iterator as $item) {
                if ($item->isDot()) {
                    continue;
                }
                if ($item->getBasename() === basename($cockpitRoot)) {
                    continue;
                }
                $destForRecursive = rtrim($destinationPathInTemp, '/') . '/' . $item->getBasename();
                if (!$this->copyRecursive(
                    $item->getRealPath(),
                    $destForRecursive,
                    $exclusions,
                    $addedPaths,
                    $currentBackupTempDir
                )) {
                    throw new \Exception(sprintf(t('Failed to copy Site part file: %s'), $item->getRealPath()));
                }
            }
        } else {
            $destForRecursive = $destinationPathInTemp;
            if (!$this->copyRecursive(
                $sourcePath,
                $destForRecursive,
                $exclusions,
                $addedPaths,
                $currentBackupTempDir
            )) {
                throw new \Exception(sprintf(t('Failed to copy Core part files. Source: %s'), $sourcePath));
            }
        }
        return $addedPaths;
    }

    public function backupDatabase($backupTempDir)
    {
        if ($this->app->dataStorage->type === 'mongolite') {
            throw new \Exception(t('Mongolite database is included in the "Core" backup and does not require a separate dump.'));
        }
        if ($this->app->dataStorage->type !== 'mongodb') {
            throw new \Exception(sprintf(t('Unsupported database type for separate backup: %s'), $this->app->dataStorage->type));
        }

        $defaultDsn = 'mongolite://' . rtrim($this->app->path('#storage:'), '/') . '/data';
        $dsn = (string)$this->app->retrieve('database/server', $this->app->retrieve('datastore/server', $defaultDsn));
        $dbDumpDir = $backupTempDir . '/database_dump';
        $nativeDbDumpDir = str_replace('/', DIRECTORY_SEPARATOR, $dbDumpDir);

        if (!$this->app->helper('fs')->mkdir($nativeDbDumpDir)) {
            throw new \Exception(sprintf(t('Failed to create database dump directory: %s'), $nativeDbDumpDir));
        }

        if ($this->app->dataStorage->type === 'mongodb') {
            if (!function_exists('shell_exec')) {
                throw new \Exception(t('shell_exec function is disabled. MongoDB backup is not possible.'));
            }

            $dbConfig = $this->app->retrieve('database');
            $dbOptions = is_array($dbConfig) ? ($dbConfig['options'] ?? []) : [];
            $dbName = trim($dbOptions['db'] ?? 'cockpitdb', '/');
            $dumpFile = $dbDumpDir . '/db.archive';
            $nativeDumpFile = str_replace('/', DIRECTORY_SEPARATOR, $dumpFile);

            $command = sprintf(
                'mongodump --uri="%s" --db="%s" --archive="%s" --quiet',
                escapeshellarg($dsn),
                escapeshellarg($dbName),
                escapeshellarg($nativeDumpFile)
            );

            $output = shell_exec($command . ' 2>&1');

            if (!file_exists($nativeDumpFile) || filesize($nativeDumpFile) === 0) {
                throw new \Exception(sprintf(t('Failed to create MongoDB backup. Please ensure mongodump utility is installed and accessible. Error output: %s'), $output ?: 'No output.'));
            }
            return $dumpFile;
        }

        throw new \Exception(sprintf(t('Unsupported database DSN: %s'), $dsn));
    }

    public function copyRecursive($sourcePath, $destinationPath, $exclusions, &$addedPaths, ?string $currentBackupTempDir = null)
    {
        $realSourcePath = realpath($sourcePath);
        if (!$realSourcePath) {
            throw new \Exception(sprintf(t('Source path not found or not accessible for recursive copy: %s'), $sourcePath));
        }
        $sourcePath = $realSourcePath;

        if (empty($destinationPath)) {
            throw new \Exception(sprintf(t('Internal error: Destination path for copy operation cannot be empty. Source: %s'), $sourcePath));
        }
        $nativeDestinationPath = str_replace('/', DIRECTORY_SEPARATOR, $destinationPath);
        $normalizedSourcePath = strtolower(str_replace('\\', '/', $sourcePath));

        if ($currentBackupTempDir) {
            $normalizedCurrentBackupTempDir = strtolower(rtrim(str_replace('\\', '/', $currentBackupTempDir), '/'));
            if ($normalizedSourcePath === $normalizedCurrentBackupTempDir || str_starts_with($normalizedSourcePath, $normalizedCurrentBackupTempDir . '/')) {
                if (!in_array($sourcePath, $addedPaths, true)) {
                    $addedPaths[] = $sourcePath;
                }
                return true;
            }
        }

        foreach ($exclusions as $ex) {
            if ($normalizedSourcePath === $ex || str_starts_with($normalizedSourcePath, $ex . '/')) {
                if (!in_array($sourcePath, $addedPaths, true)) {
                    $addedPaths[] = $sourcePath;
                }
                return true;
            }
        }

        if (in_array($sourcePath, $addedPaths, true)) {
            return true;
        }
        $addedPaths[] = $sourcePath;

        if (is_dir($sourcePath)) {
            if (!$this->app->helper('fs')->mkdir($nativeDestinationPath)) {
                throw new \Exception(sprintf(t('Failed to create destination directory "%s". Source: %s'), $nativeDestinationPath, $sourcePath));
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                $filePath = $file->getRealPath();
                if (!$filePath) {
                    $this->app->trigger('backup.warning', ['message' => sprintf(t('Skipping inaccessible file or directory: %s'), $file->getPathname())]);
                    continue;
                }

                $normalizedFilePath = strtolower(str_replace('\\', '/', $filePath));

                if ($currentBackupTempDir) {
                    if ($normalizedFilePath === $normalizedCurrentBackupTempDir || str_starts_with($normalizedFilePath, $normalizedCurrentBackupTempDir . '/')) {
                        if (!in_array($filePath, $addedPaths, true)) {
                            $addedPaths[] = $filePath;
                        }
                        continue;
                    }
                }

                $isExcluded = false;
                foreach ($exclusions as $ex) {
                    if ($normalizedFilePath === $ex || str_starts_with($normalizedFilePath, $ex . '/')) {
                        $isExcluded = true;
                        break;
                    }
                }
                if ($isExcluded) {
                    if (!in_array($filePath, $addedPaths, true)) {
                        $addedPaths[] = $filePath;
                    }
                    continue;
                }

                $relativePath = substr($filePath, strlen($sourcePath) + 1);
                $fileDestination = rtrim($destinationPath, '/') . '/' . $relativePath;

                if (empty($fileDestination)) {
                    throw new \Exception(sprintf(t('Internal error: File destination path cannot be empty for file: %s'), $filePath));
                }
                $nativeFileDestination = str_replace('/', DIRECTORY_SEPARATOR, $fileDestination);

                if ($file->isDir()) {
                    if (!$this->app->helper('fs')->mkdir($nativeFileDestination)) {
                        throw new \Exception(sprintf(t('Failed to create sub-directory "%s". Source: %s'), $nativeFileDestination, $filePath));
                    }
                } elseif ($file->isFile()) {
                    if (!$this->copyRecursiveSafe($filePath, $nativeFileDestination, false)) {
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

    protected function copyRecursiveSafe(string $source, string $destination, bool $recursive = true): bool
    {
        $nativeSource = str_replace('/', DIRECTORY_SEPARATOR, $source);
        $nativeDestination = str_replace('/', DIRECTORY_SEPARATOR, $destination);

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

            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($nativeSource, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
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

    public function restoreTarGzBackup($filename, $backupDir)
    {
        $cockpitRoot = $this->app->path('#root:');
        $projectRoot = dirname($cockpitRoot);
        $file = $backupDir . '/' . basename($filename);
        $nativeFile = str_replace('/', DIRECTORY_SEPARATOR, $file);

        if (!file_exists($nativeFile)) {
            throw new \Exception(sprintf(t('The backup file was not found: %s'), $file));
        }

        $tempExtractDir = $this->createTempDir();
        $nativeTempExtractDir = str_replace('/', DIRECTORY_SEPARATOR, $tempExtractDir);
        $nativeTempSourceDir = $nativeTempExtractDir . DIRECTORY_SEPARATOR . 'source';

        try {
            $phar = new PharData($nativeFile);
            $phar->extractTo($nativeTempSourceDir, null, true);

            $manifestFilePath = $nativeTempSourceDir . DIRECTORY_SEPARATOR . 'backup_manifest.json';
            if (!file_exists($manifestFilePath)) {
                throw new \Exception(sprintf(t('Backup manifest file not found in the archive. Path: %s'), $manifestFilePath));
            }

            $manifest = json_decode(file_get_contents($manifestFilePath), true, 512, JSON_THROW_ON_ERROR);

            $dbType = $manifest['database']['type'] ?? null;
            $dbDsn = $manifest['database']['dsn'] ?? null;
            $dbName = $manifest['database']['db_name'] ?? null;
            $dbDumpRelativePath = $manifest['paths']['database_dump_relative_path'] ?? 'database_dump';

            $extractedCockpitRoot = $nativeTempSourceDir . DIRECTORY_SEPARATOR . 'cockpit';
            $extractedProjectRootFiles = $nativeTempSourceDir;

            $nativeCockpitRoot = str_replace('/', DIRECTORY_SEPARATOR, $cockpitRoot);
            $nativeProjectRoot = str_replace('/', DIRECTORY_SEPARATOR, $projectRoot);

            if (is_dir($extractedCockpitRoot)) {
                if ($this->app->helper('fs')->remove($nativeCockpitRoot)) {
                    if (!$this->copyRecursiveSafe($extractedCockpitRoot, $nativeCockpitRoot)) {
                        throw new \Exception(t('Failed to restore Cockpit files.'));
                    }
                } else {
                    throw new \Exception(t('Failed to clear current Cockpit installation before restore.'));
                }
            }

            $items = new \DirectoryIterator($extractedProjectRootFiles);
            foreach ($items as $item) {
                if ($item->isDot() || $item->getBasename() === 'cockpit' || $item->getBasename() === $dbDumpRelativePath || $item->getBasename() === 'backup_manifest.json') {
                    continue;
                }
                $targetPath = $nativeProjectRoot . DIRECTORY_SEPARATOR . $item->getBasename();
                if ($this->app->helper('fs')->remove($targetPath)) {
                    if (!$this->copyRecursiveSafe($item->getRealPath(), $targetPath)) {
                        throw new \Exception(sprintf(t('Failed to restore project file: %s'), $item->getBasename()));
                    }
                } else {
                    throw new \Exception(sprintf(t('Failed to clear current project item "%s" before restore.'), $item->getBasename()));
                }
            }

            $extractedDbDumpPath = $nativeTempSourceDir . DIRECTORY_SEPARATOR . $dbDumpRelativePath;

            if ($dbType === 'mongodb') {
                if (!function_exists('shell_exec')) {
                    throw new \Exception(t('shell_exec function is disabled. MongoDB restore is not possible.'));
                }

                $dbArchiveFile = $extractedDbDumpPath . DIRECTORY_SEPARATOR . 'db.archive';
                $nativeDbArchiveFile = str_replace('/', DIRECTORY_SEPARATOR, $dbArchiveFile);

                if (!file_exists($nativeDbArchiveFile)) {
                    throw new \Exception(t('MongoDB archive file not found in backup for restoration.'));
                }
                if (empty($dbDsn) || empty($dbName)) {
                    throw new \Exception(t('Database DSN or name not found in manifest. Cannot restore MongoDB.'));
                }

                $command = sprintf(
                    'mongorestore --uri="%s" --db="%s" --archive="%s" --drop --quiet',
                    escapeshellarg($dbDsn),
                    escapeshellarg($dbName),
                    escapeshellarg($nativeDbArchiveFile)
                );

                $output = shell_exec($command . ' 2>&1');
                if (str_contains($output, 'Failed') || str_contains($output, 'error')) {
                    throw new \Exception(sprintf(t('MongoDB restore failed. Error output: %s'), $output));
                }

            } else if ($dbType === 'mongolite') {
                $this->app->trigger('backup.info', ['message' => t('Mongolite database restoration is implicitly handled by "Core" files restoration. No separate database dump restore needed.')]);
            } else {
                $this->app->trigger('backup.warning', ['message' => sprintf(t('Unsupported database type in manifest for separate restore: %s'), $dbType)]);
            }

            $this->app->helper('cache')->clear();
            return true;

        } catch (\Exception $e) {
            throw new \Exception(sprintf(t('Failed to restore backup: %s'), $e->getMessage()));
        } finally {
            $this->app->helper('fs')->remove($nativeTempExtractDir);
        }
    }

    public function createTempDir()
    {
        $baseTempPath = rtrim(str_replace('\\', '/', $this->app->path('#tmp:')), '/');
        $tempDir = $baseTempPath . '/' . uniqid('cockpit_backup_', true);
        $nativeTempDir = str_replace('/', DIRECTORY_SEPARATOR, $tempDir);

        if (!$this->app->helper('fs')->mkdir($nativeTempDir)) {
            throw new \Exception(sprintf(t('Failed to create temporary backup directory: %s'), $nativeTempDir));
        }

        $tempSourceDir = $tempDir . '/source';
        $nativeTempSourceDir = str_replace('/', DIRECTORY_SEPARATOR, $tempSourceDir);

        if (!$this->app->helper('fs')->mkdir($nativeTempSourceDir)) {
            throw new \Exception(sprintf(t('Failed to create temporary source subdirectory: %s'), $nativeTempSourceDir));
        }

        return $tempDir;
    }
}
