<?php
$backups = $this->module('backup')->getBackups();
$settings = $this->module('backup')->getSettings();
?>

<script type="module">
    const VIEW_LIST = 'list';
    const VIEW_SETTINGS = 'settings';
    const VIEW_PROGRESS = 'progress';

    const TAB_NAMES_MAP = {
        0: VIEW_LIST,
        1: VIEW_SETTINGS
    };

    export default {
        data() {
            const settings = <?= json_encode($settings, JSON_THROW_ON_ERROR) ?>;

            const getValidatedPaths = (pathsData) => {
                if (Array.isArray(pathsData)) {
                    return pathsData;
                }

                if (pathsData && typeof pathsData === 'object') {
                    return Object.values(pathsData);
                }

                return [];
            };

            let nextExclusionIdCounter = 0;

            const initialExclusions = Array.isArray(settings.exclusions)
                ? settings.exclusions
                    .filter(value => typeof value === 'string' && value.trim() !== '')
                    .map(value => ({
                        id: nextExclusionIdCounter++,
                        value: value
                    }))
                : [];

            return {
                backups: <?= json_encode($backups, JSON_THROW_ON_ERROR) ?>,
                loading: false,
                paths: getValidatedPaths(settings.paths),
                exclusions: initialExclusions,
                nextExclusionId: nextExclusionIdCounter,
                originalData: null,
                state: {
                    view: VIEW_LIST,
                    message: '',
                    details: '',
                    finished: false,
                    progress: null,
                    isError: false,
                },
                tabNameMap: TAB_NAMES_MAP
            };
        },

        mounted() {
            this.originalData = this.getCurrentData();
        },

        computed: {
            activePaths() {
                return this.paths.filter(path => path.active);
            },

            hasChanges() {
                return JSON.stringify(this.getCurrentData()) !== JSON.stringify(this.originalData);
            },

            totalSize() {
                return this.backups.reduce((total, backup) => total + backup.size, 0);
            }
        },

        methods: {
            getCurrentData() {
                try {
                    const currentPaths = this.paths.map(path => ({
                        code: String(path?.code || ''),
                        active: Boolean(path?.active)
                    }));
                    const currentExclusions = this.exclusions
                        .map(exclusion => String(exclusion?.value || ''))
                        .filter(value => value.trim().length > 0);
                    return {
                        paths: currentPaths,
                        exclusions: currentExclusions
                    };
                }
                catch (error) {
                    console.error('Error in getCurrentData:', error);
                    return {
                        paths: [],
                        exclusions: []
                    };
                }
            },

            async runTask(url, params = null, config = {}) {
                this.state.view = VIEW_PROGRESS;
                this.state.message = config.message || App.i18n.get('Processing...');
                this.state.details = config.details || '';
                this.state.finished = false;
                this.state.progress = config.progress || null;
                this.state.isError = false;
                this.toggleLoading(true);

                try {
                    const response = await App.request(url, params, 'post');
                    this.state.message = response.message || App.i18n.get('Task completed successfully!');
                    this.state.details = '';
                    this.state.finished = true;
                    return response;
                }
                catch (error) {
                    this.state.message = App.i18n.get('Error: ') + (error.message || App.i18n.get('Unknown error'));
                    this.state.details = '';
                    this.state.finished = true;
                    this.state.isError = true;
                    throw error;
                }
            },

            finishTask() {
                this.state.view = VIEW_LIST;
                this.state.message = '';
                this.state.details = '';
                this.state.finished = false;
                this.state.progress = null;
                this.state.isError = false;
                this.toggleLoading(false);
                this.refreshBackups();
            },

            async createBackup() {
                try {
                    await this.runTask('/backup/create', null, {
                        message: App.i18n.get('Creating a backup... It may take a few minutes'),
                        details: App.i18n.get('Please do not close this page')
                    });
                }
                catch (error) {
                    this.showNotification(App.i18n.get('Failed to create backup: ') + (error.message || App.i18n.get('Unknown error')), 'error');
                }
            },

            async refreshBackups() {
                this.toggleLoading(true);
                try {
                    const response = await App.request('/backup/getBackupsList');

                    if (response.success) {
                        this.backups = response.backups;
                        this.showNotification(App.i18n.get('Backup list updated'), 'success');
                    } else {
                        this.showNotification(response.message || App.i18n.get('Failed to refresh backups'), 'error');
                    }
                }
                catch (error) {
                    this.showNotification(App.i18n.get('Failed to refresh backups: ') + (error.message || App.i18n.get('Network error')), 'error');
                }
                finally {
                    this.toggleLoading(false);
                }
            },

            async deleteBackup(filename) {
                const confirmed = await this.confirmAction(
                    App.i18n.get('Are you sure you want to delete this backup? This action cannot be undone.')
                );

                if (!confirmed) return;

                this.toggleLoading(true);

                try {
                    await App.request('/backup/delete', {file: filename}, 'post');
                    this.showNotification(App.i18n.get('Backup deleted successfully!'), 'success');
                    await this.refreshBackups();
                }
                catch (error) {
                    this.showNotification(error.message || App.i18n.get('Failed to delete backup'), 'error');
                }
                finally {
                    this.toggleLoading(false);
                }
            },

            async restoreBackup(filename) {
                const confirmed = await this.confirmAction(
                    App.i18n.get('<strong>WARNING!</strong><br><br>This will overwrite all current files with the backup version. This action cannot be undone.'),
                );

                if (!confirmed) return;

                try {
                    await this.runTask('/backup/restore', {file: filename}, {
                        message: App.i18n.get('Restoring from backup...'),
                        details: App.i18n.get('Your site will be unavailable during this process')
                    });
                }
                catch (error) {
                    this.showNotification(App.i18n.get('Failed to restore backup: ') + (error.message || App.i18n.get('Unknown error')), 'error');
                }
            },

            addExclusion() {
                this.exclusions.push({
                    id: this.nextExclusionId++,
                    value: ''
                });

                this.$nextTick(() => {
                    const inputs = document.querySelectorAll('.kiss-input');

                    if (inputs.length > 0) {
                        inputs[inputs.length - 1].focus();
                    }
                });
            },

            removeExclusion(index) {
                this.exclusions.splice(index, 1);
            },

            async saveSettings() {
                if (!this.hasChanges) {
                    this.showNotification(App.i18n.get('No changes to save'), 'info');
                    return;
                }

                this.toggleLoading(true);

                try {
                    const payload = {
                        inclusions: this.paths.filter(path => path.active).map(path => path.code),
                        exclusions: this.exclusions
                            .map(item => item.value.trim())
                            .filter(value => value)
                    };

                    if (payload.inclusions.length === 0) {
                        throw new Error(App.i18n.get('Please select at least one section to include in the backup'));
                    }

                    await App.request('/backup/save', payload, 'post');
                    this.originalData = this.getCurrentData();
                    this.showNotification(App.i18n.get('Settings saved successfully!'), 'success');
                }
                catch (error) {
                    this.showNotification(
                        error.message || App.i18n.get('Failed to save settings'),
                        'error'
                    );
                }
                finally {
                    this.toggleLoading(false);
                }
            },

            async resetToDefaults() {
                const confirmed = await this.confirmAction(
                    App.i18n.get('Are you sure you want to reset all settings to default values?')
                );

                if (!confirmed) return;

                this.paths.forEach(path => {
                    path.active = ['Core', 'Database'].includes(path.code);
                });

                this.exclusions = [];
                this.nextExclusionId = 0;
                await this.saveSettings();
            },

            toggleLoading(isLoading) {
                this.loading = isLoading;
            },

            showNotification(message, type = 'info') {
                App.ui.notify(message, type);
            },

            async confirmAction(message, title = App.i18n.get('Confirmation')) {
                return new Promise((resolve) => {
                    App.ui.confirm(message, () => resolve(true), () => resolve(false), title);
                });
            },

            handleTabClick(event) {
                const targetLink = event.target.closest('.kiss-tabs-nav-link');
                if (targetLink) {
                    const index = targetLink.getAttribute('index');

                    if (index !== null && this.tabNameMap[index] && this.state.view !== VIEW_PROGRESS) {
                        this.state.view = this.tabNameMap[index];
                    }
                }
            },

            formatSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            },

            formatDate(timestamp) {
                const date = new Date(timestamp * 1000);
                return new Intl.DateTimeFormat(App.i18n.locale || undefined, {
                    year: 'numeric', month: 'numeric', day: 'numeric',
                    hour: 'numeric', minute: 'numeric', second: 'numeric'
                }).format(date);
            },
        }
    };
</script>
