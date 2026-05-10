<?
$hasRestoreAccess = $this->helper('acl')->isAllowed('backup/restore');
?>

<tab class="kiss-margin kiss-margin-bottom animated fadeIn" caption="<?= t('List of backups') ?>" name="list">
    <kiss-card class="kiss-margin-large-bottom kiss-padding kiss-bgcolor-contrast"
         theme="shadowed"
         v-if="state.view === 'list' && paths.length"
    >
        <div class="kiss-text-upper kiss-size-small kiss-color-muted">
            <?= t('Backup configuration') ?>
        </div>

        <ul class="kiss-flex kiss-flex-middle kiss-margin-bottom kiss-padding-remove kiss-flex-wrap" gap="small">
            <li class="kiss-badge kiss-badge-outline kiss-size-small kiss-color-primary kiss-text-leading-relaxed"
                v-for="path in activePaths"
                :key="path.code"
            >
                {{ path.name }}
            </li>
        </ul>

        <div class="kiss-text-upper kiss-size-small kiss-color-muted" v-if="exclusions.length">
            <?= t('Excluded paths') ?>
        </div>

        <ul class="kiss-flex kiss-flex-middle kiss-padding-remove kiss-flex-wrap kiss-margin-remove-bottom"
            gap="small"
            v-if="exclusions.length"
        >
            <li class="kiss-badge kiss-badge-outline kiss-size-small kiss-color-primary kiss-text-monospace kiss-text-leading-relaxed"
                v-for="exclusion in exclusions"
                :key="exclusion.id"
            >
                {{ exclusion.value }}
            </li>
        </ul>
    </kiss-card>

    <div class="kiss-margin-small-bottom kiss-flex kiss-flex-middle kiss-flex-between">
        <span class="kiss-text-bold">
            <?= t('Backups') ?>
        </span>

        <span class="kiss-size-small kiss-color-muted animated fadeIn" v-if="backups.length">
            {{ t('Total weight: ') }} {{ formatSize(totalSize) }}
        </span>
    </div>

    <div class="kiss-padding-larger kiss-align-center kiss-size-large kiss-color-muted animated fadeIn"
         v-if="!backups.length"
    >
        <?= t('Backups were not found') ?>
    </div>

    <table class="kiss-table animated fadeIn" v-if="backups.length">
        <thead>
        <tr>
            <th width="30" class="kiss-align-center">
                №
            </th>

            <th class="kiss-align-center">
                <?= t('File name') ?>
            </th>

            <th width="100" class="kiss-align-center">
                <?= t('Size') ?>
            </th>

            <th width="150" class="kiss-align-center">
                <?= t('Date') ?>
            </th>

            <th width="150" class="kiss-align-center">
                <?= t('Actions') ?>
            </th>
        </tr>
        </thead>

        <tbody>
        <tr class="kiss-transition animated fadeIn" v-for="(backup, index) in backups" :key="backup.name">
            <td class="kiss-align-center">
                {{ index + 1 }}
            </td>

            <td class="kiss-text-truncate kiss-text-monospace">
                {{ backup.name }}
            </td>

            <td class="kiss-align-center">
                <div class="kiss-badge kiss-size-xsmall kiss-text-leading-relaxed">
                    {{ formatSize(backup.size) }}
                </div>
            </td>

            <td class="kiss-color-muted kiss-size-small kiss-align-center">
                {{ formatDate(backup.created) }}
            </td>

            <td>
                <div class="kiss-flex kiss-flex-around">
                    <a class="kiss-size-3 kiss-transition"
                       :href="$baseUrl('/backup/downloadBackup?file='+backup.name)"
                       :title="t('Download a backup')"
                       target="_blank"
                    >
                        <icon>download</icon>
                    </a>

                    <? if ($hasRestoreAccess): ?>
                        <a class="kiss-size-3 kiss-transition"
                           href="#"
                           @click.prevent="restoreBackup(backup.name)"
                           :title="t('Restore a backup')"
                        >
                            <icon>history</icon>
                        </a>
                    <? endif; ?>

                    <a class="kiss-size-3 kiss-transition"
                       href="#"
                       @click.prevent="deleteBackup(backup.name)"
                       :title="t('Delete a backup')"
                    >
                        <icon>delete</icon>
                    </a>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <? if ($hasRestoreAccess): ?>
        <kiss-card class="kiss-margin-large-top kiss-padding kiss-bgcolor-contrast kiss-color-muted"
                   theme="shadowed"
        >
            <div class="kiss-size-large kiss-margin-bottom">
                <?= t('Restore script file: ') ?>

                <a class="kiss-text-monospace kiss-transition"
                   :href="$baseUrl('/backup/downloadRestoreScript')"
                   target="_blank"
                >
                    restore.php
                </a>
            </div>

            <div class="kiss-size-large">
                <?= t('Restore script usage instructions') ?>
            </div>

            <div class="kiss-padding">
                <div class="kiss-text-bold">
                    <?= t('File preparation') ?>
                </div>

                <ul>
                    <li>
                        <?= t('Download the file <span class="kiss-text-monospace">restore.php</span> and your <span class="kiss-text-monospace">.tar.gz</span> backup file.') ?>
                    </li>

                    <li>
                        <?= t('Upload BOTH files to the ROOT directory of your site (e.g., to <span class="kiss-text-monospace">/public_html</span>).') ?>
                    </li>
                </ul>

                <div class="kiss-text-bold">
                    <?= t('Starting the restoration process') ?>
                </div>

                <ul>
                    <li>
                        <?= t('Open <span class="kiss-text-monospace">restore.php</span> in your browser (e.g., <span class="kiss-text-monospace" style="word-break: break-word;">https://yourdomain.ru/restore.php</span>).') ?>
                    </li>

                    <li>
                        <?= t('The script will automatically detect backups and suggest the most recent one.') ?>
                    </li>

                    <li>
                        <?= t('Check the backup details and restoration parameters. Adjust default paths if necessary.') ?>
                    </li>

                    <li>
                        <?= t('Click') ?> <span class="kiss-text-bold">"<?= t('Start restoration') ?>"</span>.
                    </li>
                </ul>

                <div class="kiss-text-bold">
                    <?= t('Completion') ?>
                </div>

                <ul class="kiss-margin-remove-bottom">
                    <li>
                        <?= t('After the script completes, the restoration logs and buttons') ?>
                        <span class="kiss-text-bold">"<?= t('Start over') ?>"</span> и <span
                            class="kiss-text-bold">"<?= t('Finish') ?>"</span>.
                    </li>
                </ul>
            </div>
        </kiss-card>
    <? endif; ?>
</tab>
