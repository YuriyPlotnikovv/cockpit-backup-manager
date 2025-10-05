<?php
$view = $view ?? 'index';
?>

<div class="kiss-margin-large">
    <kiss-container>
        <kiss-row gap="large" class="kiss-flex kiss-text-center">
            <a href="<?= $this->route('/backup/index') ?>" gap="small"
               class="kiss-flex kiss-flex-middle kiss-flex-center kiss-text-decoration-none
                          <?= $view === 'index' ? 'kiss-text-bold kiss-color-primary' : 'kiss-color-muted' ?>">
                <kiss-svg src="<?= $this->base('system:assets/icons/list.svg') ?>" width="24" height="24"></kiss-svg>
                <?= t('List of backups') ?>
            </a>
            <a href="<?= $this->route('/backup/settings') ?>" gap="small"
               class="kiss-flex kiss-flex-middle kiss-flex-center kiss-text-decoration-none
                          <?= $view === 'settings' ? 'kiss-text-bold kiss-color-primary' : 'kiss-color-muted' ?>">
                <kiss-svg src="<?= $this->base('system:assets/icons/settings.svg') ?>" width="24" height="24"></kiss-svg>
                <?= t('Backup settings') ?>
            </a>
        </kiss-row>
    </kiss-container>
</div>
