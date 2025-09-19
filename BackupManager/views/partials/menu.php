<?php
    $view = $view ?? 'index';
?>
<div class="kiss-margin-large">
    <kiss-container>
        <kiss-row gap="large">
            <div class="kiss-position-relative kiss-flex kiss-flex-middle <?=($view === 'index' ? 'kiss-text-bold' : 'kiss-color-muted')?>">
                <div><kiss-svg src="<?=$this->base('system:assets/icons/list.svg')?>" width="30" height="30"><canvas width="30" height="30"></canvas></kiss-svg></div>
                <div class="kiss-margin-small-left"><?=t('Backups list')?></div>
                <a class="kiss-cover" href="<?=$this->route('/backupmanager/index')?>"></a>
            </div>

            <div class="kiss-position-relative kiss-flex kiss-flex-middle <?=($view === 'settings' ? 'kiss-text-bold' : 'kiss-color-muted')?>">
                <div><kiss-svg src="<?=$this->base('system:assets/icons/settings.svg')?>" width="30" height="30"><canvas width="30" height="30"></canvas></kiss-svg></div>
                <div class="kiss-margin-small-left"><?=t('Settings')?></div>
                <a class="kiss-cover"  href="<?=$this->route('/backupmanager/settings')?>"></a>
            </div>
        </kiss-row>
    </kiss-container>
</div>
