<?php

use Backup\Controller\Backup;

$this->helper('acl')->addPermissions([
    'backup' => [
        'manage' => t('Backup management'),
    ],
]);

if ($this->helper('acl')->hasPermission('backup/manage')) {

    $this->bindClass(Backup::class, '/backup');

    $this->bind('/backup', function () {
        return $this->invoke(Backup::class, 'index');
    });

    $this->on('app.settings.collect', function ($settings) {
        $settings['Extensions'][] = [
            'icon' => 'backup:icon.svg',
            'route' => '/backup',
            'label' => t('Backup'),
            'permission' => 'backup/manage',
        ];
    });
}
