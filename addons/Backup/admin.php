<?php

use Backup\Controller\Backup;

$this->on('app.permissions.collect', function ($permissions) {
    $permissions['Backup'] = [
        'backup/manage' => 'Manage backups',
        'backup/restore' => 'Restore from backup',
    ];
});

$this->bindClass(Backup::class, '/backup');

$this->on('app.settings.collect', function ($settings) {
    $settings['Addons'][] = [
        'icon' => 'backup:icon.svg',
        'route' => '/backup',
        'label' => t('Backup'),
        'permission' => 'backup/manage',
    ];
});
