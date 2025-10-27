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

    'getInfo' => function () {
        $file = $this->app->path('backup:info.json');

        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }

        return [];
    },

    'getBackupDir' => function ($create = true) {
        $path = $this->config('backup_path');

        if (empty($path) || !is_string($path)) {
            throw new \Exception(t('The path for saving backups is not configured or incorrect.'));
        }

        $normalizedPath = $this->app->helper('backup')->normalizePath($path, false);

        if ($create && !is_dir($normalizedPath) && !$this->app->helper('fs')->mkdir($normalizedPath)) {
            throw new \Exception(sprintf(t('Directory "%s" was not created'), $normalizedPath));
        }

        return $normalizedPath;
    },

    'getBackups' => function () {
        $dir = $this->getBackupDir(false);
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
            'mongoshPath' => '',
            'mongodumpPath' => '',
            'mongorestorePath' => '',
        ]);

        foreach ($availablePaths as $name => &$path) {
            $path['active'] = in_array($name, $savedSettings['inclusions'], true);
        }
        unset($path);

        $allSettings = [
            'paths' => $availablePaths,
            'exclusions' => $savedSettings['exclusions'] ?? [],
            'mongoshPath' => $savedSettings['mongoshPath'] ?? '',
            'mongodumpPath' => $savedSettings['mongodumpPath'] ?? '',
            'mongorestorePath' => $savedSettings['mongorestorePath'] ?? '',
            'isMongoDB' => $type === 'mongodb',
        ];

        if ($type === 'mongodb') {
            $allSettings['mongoToolsStatus'] = [
                'mongosh_available' => (bool)$this->app->helper('backup')->getMongoToolBinary('mongosh', $allSettings['mongoshPath']),
                'mongodump_available' => (bool)$this->app->helper('backup')->getMongoToolBinary('mongodump', $allSettings['mongodumpPath']),
                'mongorestore_available' => (bool)$this->app->helper('backup')->getMongoToolBinary('mongorestore', $allSettings['mongodumpPath']),
            ];
        }

        return $allSettings;
    },

    'createBackup' => function () {
        $backupDir = $this->getBackupDir(true);
        $backupFile = $this->app->helper('backup')->normalizePath($backupDir . DIRECTORY_SEPARATOR . 'backup_' . date('Y-m-d_H-i-s') . '.tar.gz', false);
        $settings = $this->getSettings();

        return $this->app->helper('backup')->createBackup($backupFile, $settings);
    },

    'restoreBackup' => function ($filename) {
        $backupDir = $this->getBackupDir(false);

        return $this->app->helper('backup')->restoreBackup($filename, $backupDir);
    },

    'deleteBackup' => function (string $filename) {
        $backupDir = $this->getBackupDir(false);
        $filePath = $this->app->helper('backup')->normalizePath($backupDir . DIRECTORY_SEPARATOR . basename($filename), false);

        if (!file_exists($filePath)) {
            throw new \Exception(sprintf(t('The backup file was not found: %s'), $filename));
        }

        return $this->app->helper('fs')->delete($filePath);
    },

    'getRestoreScriptPath' => function () {
        $scriptPath = $this->app->helper('backup')->normalizePath(__DIR__ . DIRECTORY_SEPARATOR . 'restore.php', false);

        if (!file_exists($scriptPath)) {
            throw new \Exception(sprintf(t('Restore script file not found at: %s'), $scriptPath));
        }

        return $scriptPath;
    },
]);
