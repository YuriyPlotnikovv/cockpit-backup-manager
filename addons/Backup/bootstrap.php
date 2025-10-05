<?php

use Backup\Helper\BackupManager;

$this->helpers['backup'] = BackupManager::class;

$this->on('app.admin.init', function () {
    include(__DIR__ . '/admin.php');
});

$this->module('backup')->extend([
    'config' => function (?string $key = null, $default = null) {
        $userConfigBlock = $this->app->retrieve('backup', []);
        $userConfig = $userConfigBlock['config'] ?? [];

        if (!is_array($userConfig)) {
            $userConfig = [];
        }

        $config = array_replace_recursive([
            'backup_path' => rtrim($this->app->path('#root:'), '/') . '/backups',
        ], $userConfig);

        return $key ? ($config[$key] ?? $default) : $config;
    },

    'getBackupDir' => function ($create = true) {
        $path = $this->config('backup_path');

        if (empty($path) || !is_string($path)) {
            throw new \Exception(t('The path for saving backups is not configured or incorrect.'));
        }

        if ($create && !is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new \Exception(sprintf(t('Directory "%s" was not created'), $path));
        }

        return $path;
    },
    'getBackups' => function () {
        try {
            $dir = $this->getBackupDir(false);
        } catch (\Exception $e) {
            return [];
        }

        $files = $this->app->helper('fs')->ls('*.tar.gz', $dir);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => $file->getBasename(),
                'size' => $file->getSize(),
                'created' => $file->getMTime(),
                'path' => $file->getRealPath(),
            ];
        }

        usort($backups, static fn($a, $b) => $b['created'] <=> $a['created']);

        return $backups;
    },

    'getSettings' => function () {
        $projectRoot = dirname($this->app->path('#root:'));
        $type = $this->app->dataStorage->type;

        $availablePaths = [
            'Core' => [
                'name' => t('Core'),
                'code' => 'Core',
                'description' => t('Cockpit CMS system files'),
                'path' => $this->app->path('#root:'),
                'icon' => 'system:assets/icon-sets/System/settings-3-line.svg',
            ],
            'Site' => [
                'name' => t('Public'),
                'code' => 'Site',
                'description' => t('Site files (all except the /cockpit folder)'),
                'path' => $projectRoot,
                'icon' => 'system:assets/icon-sets/Business/window-line.svg',
            ],
            'Database' => [
                'name' => t('Database'),
                'code' => 'Database',
                'description' => t('Current driver: ') . $type,
                'path' => null,
                'icon' => 'system:assets/icon-sets/Device/database-2-line.svg',
            ],
        ];

        $savedSettings = $this->app->dataStorage->getKey('backup', 'settings', [
            'inclusions' => ['Core'],
            'exclusions' => [],
        ]);

        foreach ($availablePaths as $name => &$path) {
            $path['_active'] = in_array($name, $savedSettings['inclusions'], true);
        }

        return [
            'paths' => $availablePaths,
            'exclusions' => $savedSettings['exclusions'] ?? [],
        ];
    },

    'create' => function () {
        $projectRoot = dirname($this->app->path('#root:'));
        $cockpitRoot = $this->app->path('#root:');
        $backupDir = $this->getBackupDir(true);
        $backupFile = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.tar.gz';
        $settings = $this->getSettings();
        $activePaths = array_filter($settings['paths'], static fn($p) => $p['_active']);

        if (empty($activePaths)) {
            throw new \Exception(t('No parts selected for inclusion in the backup.'));
        }

        $backupTempDir = $this->app->helper('backup')->createTempDir();
        $tempSourceDir = $backupTempDir . '/source';

        try {
            $this->app->helper('backup')->createBackupManifest($tempSourceDir);

            $exclusions = $this->app->helper('backup')->prepareExclusions(
                $projectRoot,
                $backupDir,
                $settings['exclusions'],
                $backupTempDir
            );

            $addedPaths = [];

            foreach ($activePaths as $part) {
                $this->app->helper('backup')->copyBackupPartToTemp(
                    $tempSourceDir,
                    $part,
                    $projectRoot,
                    $cockpitRoot,
                    $exclusions,
                    $addedPaths,
                    $backupTempDir
                );
            }

            $this->app->helper('backup')->createTarGzArchive($backupFile, $tempSourceDir);

            return true;
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->app->helper('backup')->removeRecursive($backupTempDir);
        }
    },

    'deleteBackup' => function (string $filename) {
        $backupDir = $this->getBackupDir(false);
        $filePath = rtrim(str_replace('\\', '/', $backupDir), '/') . '/' . basename($filename);

        if (!file_exists($filePath)) {
            throw new \Exception(sprintf(t('The backup file was not found: %s'), $filename));
        }

        if (!$this->app->helper('backup')->removeRecursive($filePath)) {
            throw new \Exception(sprintf(t('Failed to delete file: %s'), $filename));
        }

        return true;
    },

    'restore' => function ($filename) {
        $backupDir = $this->getBackupDir(false);

        return $this->app->helper('backup')->restoreTarGzBackup($filename, $backupDir);
    },

    'getRestoreScriptPath' => function () {
        $scriptPath = __DIR__ . '/restore.php';

        if (!file_exists($scriptPath)) {
            throw new \Exception(sprintf(t('Restore script file not found at: %s'), $scriptPath));
        }

        return $scriptPath;
    },
]);
