<?php

$this->helper('acl')->addPermissions([
    'backupmanager' => [
        'manage' => 'Управление резервными копиями',
    ],
]);

if ($this->helper('acl')->hasPermission('backupmanager/manage')) {

    $this->bindClass('BackupManager\\Controller\\BackupManager', '/backupmanager');

    $this->bind('/backupmanager', function () {
//        $this->script('backupmanager:assets/js/backupmanager.js');
        $this->style('backupmanager:assets/css/backupmanager.css');

        return $this->invoke('BackupManager\\Controller\\BackupManager', 'index');
    });

    $this->on('app.settings.collect', function ($settings) {
        $settings['Extensions'][] = [
            'icon' => 'backupmanager:icon.svg',
            'route' => '/backupmanager',
            'label' => 'Резервные копии',
            'permission' => 'backupmanager/manage',
        ];
    });
}
