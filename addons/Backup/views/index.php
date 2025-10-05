<?php
$config = $this->module('backup')->config();
$settings = $this->module('backup')->getSettings();
$backups = $this->module('backup')->getBackups();
?>

<kiss-container class="kiss-margin-small" size="medium">
    <ul class="kiss-breadcrumbs">
        <li><a href="<?= $this->route('/system') ?>"><?= t('Settings') ?></a></li>
    </ul>

    <vue-view>
        <template>
            <div class="kiss-margin-large-bottom kiss-size-3 kiss-text-bold">
                <?= t('Backup') ?>
            </div>

            <?= $this->render('backup:views/partials/menu.php') ?>

            <div class="kiss-margin-large animated fadeIn" v-if="state.view === 'list' && activePaths.length">
                <div class="kiss-card kiss-padding kiss-bgcolor-contrast">
                    <div class="kiss-text-caption kiss-margin-small-bottom kiss-color-muted">
                        <?= t('Backup configuration') ?>
                    </div>

                    <div class="kiss-flex kiss-margin-bottom kiss-flex-middle kiss-flex-wrap" gap="small">
                        <div class="kiss-badge kiss-badge-outline" v-for="path in activePaths" :key="path.code">
                            {{ path.name }}
                        </div>
                    </div>

                    <div class="kiss-text-caption kiss-margin-small-bottom kiss-color-muted">
                        <?= t('Excluded paths') ?>
                    </div>

                    <div class="kiss-flex kiss-flex-middle kiss-flex-wrap" gap="small"
                         v-if="excludedFolders.length">
                        <div class="kiss-badge kiss-badge-outline" v-for="folder in excludedFolders" :key="folder">{{
                                                                                                                   folder
                                                                                                                   }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="animated fadeIn" v-if="state.view === 'list'">
                <div class="kiss-card">
                    <div class="kiss-card-header kiss-margin-small-bottom kiss-flex kiss-flex-middle kiss-flex-between">
                        <strong><?= t('Backups') ?></strong>
                        <span class="kiss-badge kiss-margin-small-left"
                              v-if="backups.length">{{ backups.length }}</span>
                    </div>

                    <div class="kiss-card-body">
                        <div v-if="!backups.length && !loading"
                             class="kiss-padding-larger kiss-text-center kiss-color-muted">
                            <div class="kiss-size-large"><?= t('Backups were not found') ?></div>
                        </div>

                        <table class="kiss-table kiss-table-hover" v-if="backups.length">
                            <thead>
                            <tr>
                                <th><?= t('File name') ?></th>
                                <th width="120"><?= t('Size') ?></th>
                                <th width="180"><?= t('Date') ?></th>
                                <th width="140" class="kiss-text-center"><?= t('Actions') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="backup in backups" :key="backup.name" class="kiss-transition">
                                <td>
                                    <div class="kiss-flex kiss-flex-middle">
                                        <span class="kiss-text-truncate" :title="backup.name">{{ backup.name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="kiss-badge" :class="getSizeBadgeClass(backup.size)">
                                        {{ formatSize(backup.size) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="kiss-color-muted kiss-size-small">
                                        {{ formatDate(backup.created) }}
                                    </span>
                                </td>
                                <td class="kiss-flex kiss-flex-between">
                                    <a class="kiss-size-3"
                                       :href="$baseUrl('/backup/download?file='+backup.name)"
                                       :title="t('Download a backup')"
                                       target="_blank">
                                        <icon>download</icon>
                                    </a>
                                    <a class="kiss-size-3" href="#"
                                       @click.prevent="restoreBackup(backup.name)"
                                       :title="t('Restore a backup')">
                                        <icon>history</icon>
                                    </a>
                                    <a class="kiss-size-3" href="#"
                                       @click.prevent="deleteBackup(backup.name)"
                                       :title="t('Delete a backup')">
                                        <icon>delete</icon>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="">
                    <?= t('Download the restore script file: ') ?>
                    <a class="" :href="$baseUrl('/backup/downloadRestoreScript')" target="_blank">restore.php</a>
                </div>
            </div>

            <div class="animated fadeIn" v-if="state.view === 'progress'">
                <div class="kiss-card kiss-text-center">
                    <div class="kiss-card-body kiss-padding-larger">
                        <div class="kiss-margin">
                            <app-loader v-if="!state.finished"></app-loader>
                            <icon size="large" class="kiss-color-success" v-else>check_circle</icon>
                        </div>

                        <div class="kiss-margin">
                            <div class="kiss-size-large kiss-margin-small-bottom">{{ state.message }}</div>
                            <div class="kiss-size-small kiss-color-muted" v-if="state.details">
                                {{ state.details }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <teleport to="body">
                <app-actionbar>
                    <div class="kiss-container">
                        <div class="kiss-flex kiss-flex-between kiss-flex-middle">
                            <div class="kiss-flex kiss-flex-middle">
                                <div class="kiss-size-small kiss-color-muted" v-if="backups.length">
                                    {{ t('Total weight: ') }} {{ formatSize(totalSize) }}
                                </div>
                            </div>

                            <div class="kiss-flex kiss-flex-middle" gap="small">
                                <button class="kiss-button kiss-button-outline"
                                        @click="refreshBackups"
                                        :disabled="loading"
                                        v-if="state.view === 'list'"
                                        :title="t('Refresh list of backups')">
                                    <icon :class="{'kiss-animate-spin': loading}">refresh</icon>
                                </button>

                                <button class="kiss-button"
                                        :class="{'kiss-button-primary': state.view === 'list', 'kiss-button-success': state.view === 'progress'}"
                                        @click="state.view === 'list' ? createBackup() : finishTask()"
                                        :disabled="loading || (state.view === 'progress' && !state.finished)">
                                    <span v-if="state.view === 'list'">
                                        {{ loading ? t('Creating...') : t('Create a backup') }}
                                    </span>
                                    <span v-else>
                                        {{ state.finished ? t('Done') : t('Processing...') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </app-actionbar>
            </teleport>
        </template>

        <script type="module">
            export default {
                data() {
                    return {
                        backups: <?= json_encode($backups) ?>,
                        activePaths: Object.values(<?= json_encode($settings['paths']) ?>).filter(path => path._active),
                        excludedFolders: <?= json_encode($settings['exclusions']) ?>,
                        loading: false,
                        state: {
                            view: 'list',
                            message: '',
                            details: '',
                            finished: false,
                            progress: null
                        }
                    };
                },

                computed: {
                    totalSize() {
                        return this.backups.reduce((total, backup) => total + backup.size, 0);
                    }
                },

                methods: {
                    async runTask(url, params = null, config = {}) {
                        this.state.view = 'progress';
                        this.state.message = config.message || App.i18n.get('Processing...');
                        this.state.details = config.details || '';
                        this.state.finished = false;
                        this.state.progress = config.progress || null;
                        this.loading = true;

                        try {
                            const response = await App.request(url, params, 'post');
                            this.state.message = response.message || App.i18n.get('Task completed successfully!');
                            this.state.finished = true;
                            return response;
                        }
                        catch (error) {
                            this.state.message = App.i18n.get('Error: ') + (error.message || App.i18n.get('Unknown error'));
                            this.state.finished = true;
                            throw error;
                        }
                        finally {
                            this.loading = false;
                        }
                    },

                    finishTask() {
                        this.state.view = 'list';
                        this.state.message = '';
                        this.state.details = '';
                        this.state.finished = false;
                        this.state.progress = null;
                        this.refreshBackups();
                    },

                    async refreshBackups() {
                        this.loading = true;
                        try {
                            const response = await App.request('/backup/getBackupsList');
                            if (response.success) {
                                this.backups = response.backups;
                                App.ui.notify(App.i18n.get('Backup list updated'), 'success');
                            }
                        }
                        catch (error) {
                            App.ui.notify(App.i18n.get('Failed to refresh backups'), 'error');
                        }
                        finally {
                            this.loading = false;
                        }
                    },

                    async createBackup() {
                        await this.runTask('/backup/create', null, {
                            message: App.i18n.get('Creating a backup... It may take a few minutes'),
                            details: App.i18n.get('Please do not close this page')
                        });
                    },

                    async deleteBackup(filename) {
                        const confirmed = await new Promise((resolve) => {
                            App.ui.confirm(
                                App.i18n.get('Are you sure you want to delete this backup? This action cannot be undone.'),
                                () => resolve(true),
                                () => resolve(false),
                                {
                                    title: App.i18n.get('Confirm deletion'),
                                    labelOk: App.i18n.get('Yes, delete'),
                                    type: 'danger'
                                }
                            );
                        });

                        if (confirmed) {
                            this.loading = true;
                            try {
                                await App.request('/backup/delete', {file: filename}, 'post');
                                App.ui.notify(App.i18n.get('Backup deleted successfully!'), 'success');

                                await this.refreshBackups();
                            }
                            catch (error) {
                                App.ui.notify(error.message, 'error');
                            }
                            finally {
                                this.loading = false;
                            }
                        }
                    },

                    async restoreBackup(filename) {
                        const confirmed = await new Promise((resolve) => {
                            App.ui.confirm(
                                App.i18n.get('<strong>WARNING!</strong><br><br>This will overwrite all current files with the backup version. This action cannot be undone.'),
                                () => resolve(true),
                                () => resolve(false),
                                {
                                    title: App.i18n.get('Confirm restoration'),
                                    labelOk: App.i18n.get('Yes, restore'),
                                    type: 'warning'
                                }
                            );
                        });

                        if (confirmed) {
                            await this.runTask('/backup/restore', {file: filename}, {
                                message: App.i18n.get('Restoring from backup...'),
                                details: App.i18n.get('Your site will be unavailable during this process')
                            });
                        }
                    },

                    getSizeBadgeClass(size) {
                        if (size > 1024 * 1024 * 100) {
                            return 'kiss-badge-danger';
                        } else if (size > 1024 * 1024 * 50) {
                            return 'kiss-badge-warning';
                        }
                        return 'kiss-badge-success';
                    },

                    formatSize(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                    },

                    formatDate(timestamp) {
                        return new Date(timestamp * 1000).toLocaleDateString() + ' ' +
                            new Date(timestamp * 1000).toLocaleTimeString();
                    }
                }
            };
        </script>
    </vue-view>
</kiss-container>
