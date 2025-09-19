<?php

$this->on('app.admin.init', function () {
    include(__DIR__ . '/admin.php');
});

$this->module('backupmanager')->extend([
    'config' => function (?string $key = null, $default = null) {
        $userConfigBlock = $this->app->retrieve('backupmanager', []);
        $userConfig = $userConfigBlock['config'] ?? [];

        if (!is_array($userConfig)) {
            $userConfig = [];
        }

        $config = array_replace_recursive([
            'backup_path' => rtrim($this->app->path('#root:'), '/') . '/backup',
        ], $userConfig);

        return $key ? ($config[$key] ?? $default) : $config;
    },

    'getBackupDir' => function ($create = true) {
        $path = $this->config('backup_path');

        if (empty($path) || !is_string($path)) {
            throw new \Exception('Путь для сохранения резервных копий не настроен или некорректен.');
        }

        if ($create && !is_dir($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new \Exception(sprintf('Directory "%s" was not created', $path));
            }
        }

        return $path;
    },

    'getBackups' => function () {
        try {
            $dir = $this->getBackupDir(false);
        } catch (\Exception $e) {
            return [];
        }

        if (!$dir) return [];

        $files = $this->app->helper('fs')->ls('*.zip', $dir);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => $file->getBasename(),
                'size' => $file->getSize(),
                'created' => $file->getMTime(),
                'path' => $file->getRealPath(),
            ];
        }

        usort($backups, fn($a, $b) => $b['created'] <=> $a['created']);

        return $backups;
    },

    'getSettings' => function () {
        $projectRoot = dirname($this->app->path('#root:'));
        $defaultDsn = 'mongolite://' . rtrim($this->app->path('#storage:'), '/') . '/data';
        $dsn = $this->app->retrieve('database/server', $this->app->retrieve('datastore/server', $defaultDsn));
        [$driver] = explode(':', $dsn, 2);

        $availablePaths = [
            'Core' => [
                'name' => 'Core', 'description' => 'Системные файлы Cockpit CMS',
                'path' => $this->app->path('#root:'), 'icon' => 'system:assets/icons/cockpit.svg',
            ],
            'Site' => [
                'name' => 'Site', 'description' => 'Файлы сайта (все, кроме папки /cockpit)',
                'path' => $projectRoot, 'icon' => 'system:assets/icons/globe.svg',
            ],
            'Database' => [
                'name' => 'Database',
                'description' => "База данных (текущий драйвер: {$driver})",
                'path' => null,
                'icon' => 'system:assets/icons/database.svg',
            ],
        ];

        $savedSettings = $this->app->dataStorage->getKey('backupmanager', 'settings', [
            'inclusions' => ['Core', 'Database'], 'exclusions' => ['node_modules', '.git'],
        ]);

        foreach ($availablePaths as $name => &$path) {
            $path['_active'] = in_array($name, $savedSettings['inclusions']);
        }

        return ['paths' => $availablePaths, 'exclusions' => $savedSettings['exclusions'] ?? []];
    },

    'backupDatabase' => function (string $backupTempDir) {

        $defaultDsn = 'mongolite://' . rtrim($this->app->path('#storage:'), '/') . '/data';
        $dsn = $this->app->retrieve('database/server', $this->app->retrieve('datastore/server', $defaultDsn));

        if (str_starts_with($dsn, 'mongolite://')) {
            [, $dbPath] = explode('://', $dsn, 2);

            if (!is_dir($dbPath)) {
                return null;
            }

            $backupDestination = $backupTempDir . '/database_backup';
            $this->app->helper('fs')->mkdir($backupDestination);

            try {
                $this->app->helper('fs')->copy($dbPath, $backupDestination);
                return $backupDestination;
            } catch (\Exception $e) {
                throw new \Exception("Ошибка копирования директории MongoLite/SQLite из '{$dbPath}': " . $e->getMessage());
            }
        }

        if (str_starts_with($dsn, 'mongodb://')) {
            if (!function_exists('shell_exec')) {
                throw new \Exception('Функция shell_exec отключена. Бэкап MongoDB невозможен.');
            }

            $dbConfig = $this->app->retrieve('database');
            $dbName = trim($dbConfig['options']['db'] ?? 'cockpitdb', '/');
            $dumpPath = $backupTempDir . '/mongodb_dump';
            $this->app->helper('fs')->mkdir($dumpPath);
            $command = sprintf('mongodump --uri="%s" --db="%s" --archive="%s" --quiet', $dsn, $dbName, $dumpPath . '/db.archive');
            shell_exec($command);

            if (!file_exists($dumpPath . '/db.archive') || filesize($dumpPath . '/db.archive') === 0) {
                throw new \Exception('Ошибка создания бэкапа MongoDB. Убедитесь, что утилита mongodump установлена и доступна.');
            }
            return $dumpPath;
        }

        throw new \Exception("Неподдерживаемый DSN базы данных: {$dsn}");
    },

    'create' => function () {
        $projectRoot = dirname($this->app->path('#root:'));
        $backupDir = $this->getBackupDir(true);
        $backupFile = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.zip';
        $settings = $this->getSettings();
        $activePaths = array_filter($settings['paths'], fn($p) => $p['_active']);

        if (empty($activePaths)) {
            throw new \Exception('Не выбрана ни одна часть для включения в резервную копию.');
        }

        $backupTempDir = $this->app->path('#tmp:') . '/' . uniqid('backupmanager_', true);
        $this->app->helper('fs')->mkdir($backupTempDir);
        $zip = new \ZipArchive();

        if ($zip->open($backupFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->app->helper('fs')->remove($backupTempDir);
            throw new \Exception('Не удалось создать zip архив.');
        }

        try {
            $standardExclusions = [basename($this->app->path('#tmp:'))];
            $finalExclusions = array_unique(array_merge($standardExclusions, $settings['exclusions']));
            $absoluteExclusions = [];

            foreach ($finalExclusions as $exclusion) {
                $sanitized = trim($exclusion, " \t\n\r\0\x0B\\/");

                if (!empty($sanitized)) {
                    $absoluteExclusions[] = str_replace('\\', '/', $projectRoot . DIRECTORY_SEPARATOR . $sanitized);
                }
            }

            $absoluteExclusions[] = str_replace('\\', '/', $backupDir);
            $addedPaths = [];

            foreach ($activePaths as $part) {
                if ($part['name'] === 'Database') {
                    $dbBackupPath = $this->backupDatabase($backupTempDir);

                    if ($dbBackupPath) {
                        if (is_file($dbBackupPath)) {
                            $zip->addFile($dbBackupPath, 'database_backup/' . basename($dbBackupPath));
                        } elseif (is_dir($dbBackupPath)) {
                            $addedPaths = $this->addToZip($zip, $dbBackupPath, $backupTempDir, [], $addedPaths);
                        }
                    }

                    continue;
                }

                if ($part['name'] === 'Site') {
                    $items = new \DirectoryIterator($projectRoot);

                    foreach ($items as $item) {
                        if ($item->isDot() || $item->getBasename() === 'cockpit') continue;

                        $addedPaths = $this->addToZip($zip, $item->getRealPath(), $projectRoot, $absoluteExclusions, $addedPaths);
                    }
                } else {
                    $addedPaths = $this->addToZip($zip, $part['path'], $projectRoot, $absoluteExclusions, $addedPaths);
                }
            }
        } finally {
            $zip->close();
            $this->app->helper('fs')->remove($backupTempDir);
        }

        return true;
    },

    'addToZip' => function (\ZipArchive $zip, string $sourcePath, string $projectRoot, array $exclusions, array $addedPaths) {
        $sourcePath = realpath($sourcePath);

        if (!$sourcePath || in_array($sourcePath, $addedPaths)) {
            return $addedPaths;
        }

        $normalizedSourcePath = str_replace('\\', '/', $sourcePath);

        foreach ($exclusions as $ex) {
            if (str_starts_with($normalizedSourcePath, $ex)) {
                return $addedPaths;
            }
        }

        $addedPaths[] = $sourcePath;

        if (is_dir($sourcePath)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                if (!$filePath) continue;
                $normalizedFilePath = str_replace('\\', '/', $filePath);
                $isExcluded = false;

                foreach ($exclusions as $ex) {
                    if (str_starts_with($normalizedFilePath, $ex)) {
                        $isExcluded = true;
                        break;
                    }
                }

                if ($isExcluded) continue;

                if (!$file->isDir()) {
                    $relativePath = substr($filePath, strlen($projectRoot) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        } elseif (is_file($sourcePath)) {
            $relativePath = substr($sourcePath, strlen($projectRoot) + 1);
            $zip->addFile($sourcePath, $relativePath);
        }

        return $addedPaths;
    },

    'restore' => function ($filename) {
        $projectRoot = dirname($this->app->path('#root:'));
        $backupDir = $this->getBackupDir(false);
        $file = $backupDir . '/' . basename($filename);

        if (!file_exists($file)) {
            throw new \Exception("Файл бэкапа не найден: {$filename}");
        }

        $zip = new \ZipArchive;

        if ($zip->open($file) !== TRUE) {
            throw new \Exception('Не удалось открыть архив бэкапа.');
        }

        $zip->extractTo($projectRoot);
        $zip->close();

        $this->app->helper('cache')->clear();

        return true;
    },
]);
