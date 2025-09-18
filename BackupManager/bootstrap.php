<?php

$this->on('app.admin.init', function () {
    include(__DIR__ . '/admin.php');
});

$this->module('backupmanager')->extend([
    'config' => function (?string $key = null, $default = null) {

        $default_backup_path = rtrim($this->app->path('#storage:'), '/') . '/test';

        $defaults = [
            'backup_path' => $default_backup_path,
            'exclude' => [
                'test',
                'node_modules',
                'vendor',
                basename($this->app->path('#tmp:')),
            ],
        ];

        $userConfig = $this->app->retrieve('backupmanager', []);

        $finalExcludes = array_unique(array_merge(
            $defaults['exclude'],
            $userConfig['exclude'] ?? []
        ));

        $config = array_replace_recursive($defaults, $userConfig);
        $config['exclude'] = $finalExcludes;

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

    'create' => function () {
        $projectRoot = dirname($this->app->path('#root:'));
        $backupDir = $this->getBackupDir(true);

        if (!$backupDir || !is_writable($backupDir)) {
            throw new \Exception(" Директория для бэкапов недоступна для записи: {$backupDir}");
        }
        $backupFile = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.zip';
        $exclusions = array_filter($this->config('exclude', []));
        $absoluteExclusions = [];

        foreach ($exclusions as $exclusion) {
            $sanitizedExclusion = trim($exclusion, " \t\n\r\0\x0B\\/");

            if (!empty($sanitizedExclusion)) {
                $absoluteExclusions[] = $projectRoot . DIRECTORY_SEPARATOR . $sanitizedExclusion;
            }
        }

        $absoluteExclusions[] = $backupDir;

        $absoluteExclusions = array_map(function($path) {
            return str_replace('\\', '/', $path);
        }, $absoluteExclusions);

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errors.php', print_r($absoluteExclusions, true), FILE_APPEND);
        $zip = new \ZipArchive();

        if ($zip->open($backupFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Не удалось создать zip архив: {$backupFile}");
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($projectRoot, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            if ($filePath === false) continue;

            $normalizedFilePath = str_replace('\\', '/', $filePath);

            $isExcluded = false;
            foreach ($absoluteExclusions as $exclusionPath) {
                if (str_starts_with($normalizedFilePath, $exclusionPath)) {
                    $isExcluded = true;
                    break;
                }
            }
            if ($isExcluded) {
                continue;
            }

            if (!$file->isDir()) {
                $relativePath = substr($normalizedFilePath, strlen(str_replace('\\', '/', $projectRoot)) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return true;
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
