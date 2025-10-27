<?php
$moduleInfo = $this->module('backup')->getInfo();
$backups = $this->module('backup')->getBackups();
$settings = $this->module('backup')->getSettings();
$hasRestoreAccess = $this->helper('acl')->isAllowed('backup/restore');
?>

<script type="module">
    const VIEW_LIST = 'list';
    const VIEW_SETTINGS = 'settings';
    const VIEW_ABOUT = 'about';
    const VIEW_PROGRESS = 'progress';

    export default {
        data() {
            const moduleInfo = <?= json_encode($moduleInfo, JSON_THROW_ON_ERROR) ?>;
            const settings = <?= json_encode($settings, JSON_THROW_ON_ERROR) ?>;
            const backups = <?= json_encode($backups, JSON_THROW_ON_ERROR) ?>;
            const hasRestoreAccess = <?= json_encode($hasRestoreAccess, JSON_THROW_ON_ERROR) ?>;
            const validatedPathsArray = this.getValidatedPaths(settings.paths);
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
                backups: backups,
                paths: this.getValidatedPaths(settings.paths),
                exclusions: initialExclusions,
                hasRestoreAccess: hasRestoreAccess,
                nextExclusionId: nextExclusionIdCounter,
                originalData: null,
                state: {
                    view: VIEW_LIST,
                    message: '',
                    details: '',
                    isSaving: false,
                    isFinished: false,
                    isError: false,
                },
                tabNameMap: {
                    0: VIEW_LIST,
                    1: VIEW_SETTINGS,
                    2: VIEW_ABOUT,
                },
                mongoshPath: settings.mongoshPath || '',
                mongodumpPath: settings.mongodumpPath || '',
                mongorestorePath: settings.mongorestorePath || '',
                mongoToolsStatus: settings.mongoToolsStatus || {},
                isMongoDB: validatedPathsArray.some(path => path.code === 'Database' && path.description.includes('mongodb')),
                moduleInfo: moduleInfo
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
                    const currentMongoPaths = {
                        mongoshPath: this.mongoshPath,
                        mongodumpPath: this.mongodumpPath,
                        mongorestorePath: this.mongorestorePath,
                    };

                    return {
                        paths: currentPaths,
                        exclusions: currentExclusions,
                        ...currentMongoPaths
                    };
                }
                catch (error) {
                    return {
                        paths: [],
                        exclusions: [],
                        mongoshPath: '',
                        mongodumpPath: '',
                        mongorestorePath: '',
                    };
                }
            },

            async runTask(url, params = null, config = {}) {
                this.state.view = VIEW_PROGRESS;
                this.state.message = config.message || '';
                this.state.details = config.details || '';
                this.state.isFinished = false;
                this.state.isError = false;

                try {
                    const response = await App.request(url, params);

                    this.state.message = response.message;
                    this.state.details = response.details;
                    this.state.isFinished = true;

                    return response;
                }
                catch (error) {
                    this.state.message = error.message;
                    this.state.details = error.details || '';
                    this.state.isFinished = true;
                    this.state.isError = true;
                    throw error;
                }
            },

            finishTask() {
                this.state.view = VIEW_LIST;
                this.state.message = '';
                this.state.details = '';
                this.state.isFinished = false;
                this.state.isError = false;
                this.refreshBackups();
            },

            async createBackup() {
                try {
                    await this.runTask('/backup/createBackup', null, {
                        message: App.i18n.get('Creating a backup... It may take a few minutes'),
                        details: App.i18n.get('Please do not close this page')
                    });
                }
                catch (error) {
                    this.showNotification(error);
                }
            },

            async refreshBackups() {
                try {
                    const response = await App.request('/backup/getBackups');
                    this.backups = response.backups;
                }
                catch (error) {
                    this.showNotification(error);
                }
            },

            async deleteBackup(filename) {
                const confirmed = await this.confirmAction(
                    App.i18n.get('Are you sure you want to delete this backup? This action cannot be undone.')
                );

                if (!confirmed) return;

                try {
                    const response = await App.request('/backup/deleteBackup', {file: filename});
                    this.showNotification(response);
                    await this.refreshBackups();
                }
                catch (error) {
                    this.showNotification(error);
                }
            },

            async restoreBackup(filename) {
                if (!this.hasRestoreAccess) {
                    return;
                }

                const confirmed = await this.confirmAction(
                    App.i18n.get('<strong>WARNING!</strong><br><br>This will overwrite all current files with the backup version. This action cannot be undone.'),
                );

                if (!confirmed) return;

                try {
                    await this.runTask('/backup/restoreBackup', {file: filename}, {
                        message: App.i18n.get('Restoring from backup...'),
                        details: App.i18n.get('Your site will be unavailable during this process')
                    });
                }
                catch (error) {
                    this.showNotification(error);
                }
            },

            addExclusion() {
                this.exclusions.push({
                    id: this.nextExclusionId++,
                    value: ''
                });

                this.$nextTick(() => {
                    const inputs = document.querySelectorAll('[name="exclusion"]');

                    if (inputs.length > 0) {
                        inputs[inputs.length - 1].focus();
                    }
                });
            },

            removeExclusion(index) {
                this.exclusions.splice(index, 1);
            },

            async saveSettings() {
                this.saving = true;

                try {
                    const payload = {
                        inclusions: this.paths.filter(path => path.active).map(path => path.code),
                        exclusions: this.exclusions
                            .map(item => item.value.trim())
                            .filter(value => value),
                        mongoshPath: this.mongoshPath.trim(),
                        mongodumpPath: this.mongodumpPath.trim(),
                        mongorestorePath: this.mongorestorePath.trim(),
                    };

                    if (payload.inclusions.length === 0) {
                        throw new Error(App.i18n.get('Please select at least one section to include in the backup'));
                    }

                    const response = await App.request('/backup/saveSettings', payload);
                    await this.fetchSettings();
                    this.originalData = this.getCurrentData();
                    this.showNotification(response);
                }
                catch (error) {
                    this.showNotification(error);
                }
                finally {
                    this.saving = false;
                }
            },

            async fetchSettings() {
                try {
                    const response = await App.request('/backup/getSettings', {});
                    console.log(response);
                    if (response) {
                        this.paths = this.getValidatedPaths(response.paths);
                        let nextExclusionIdCounter = 0;
                        this.exclusions = Array.isArray(response.exclusions)
                            ? response.exclusions
                                .filter(value => typeof value === 'string' && value.trim() !== '')
                                .map(value => ({
                                    id: nextExclusionIdCounter++,
                                    value: value
                                }))
                            : [];
                        this.nextExclusionId = nextExclusionIdCounter;
                        this.mongoshPath = response.mongoshPath || '';
                        this.mongodumpPath = response.mongodumpPath || '';
                        this.mongorestorePath = response.mongorestorePath || '';
                        this.mongoToolsStatus = response.mongoToolsStatus || {};
                        this.isMongoDB = response.isMongoDB;
                    }
                }
                catch (error) {
                    this.showNotification(error);
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
                this.mongoshPath = '';
                this.mongodumpPath = '';
                this.mongorestorePath = '';

                await this.saveSettings();
            },

            showNotification(response) {
                const message = response.message;
                const type = response.success ? 'success' : 'error';

                App.ui.notify(message, type);
            },

            async confirmAction(message) {
                return new Promise((resolve) => {
                    App.ui.confirm(message, () => resolve(true), () => resolve(false));
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

            formatSize(totalSize) {
                if (totalSize === 0) {
                    return '0 Bytes';
                }

                const KILOBYTE_MULTIPLIER = 1024;
                const DATA_SIZE_UNITS = ['Bytes', 'KB', 'MB', 'GB'];

                const currentUnitIndex = Math.floor(Math.log(totalSize) / Math.log(KILOBYTE_MULTIPLIER));

                return parseFloat((totalSize / Math.pow(KILOBYTE_MULTIPLIER, currentUnitIndex)).toFixed(2)) + ' ' + DATA_SIZE_UNITS[currentUnitIndex];
            },

            formatDate(timestamp) {
                const date = new Date(timestamp * 1000);

                return new Intl.DateTimeFormat(App.i18n.locale || undefined, {
                    year: 'numeric', month: 'numeric', day: 'numeric',
                    hour: 'numeric', minute: 'numeric', second: 'numeric'
                }).format(date);
            },

            getValidatedPaths(pathsData) {
                if (Array.isArray(pathsData)) {
                    return pathsData;
                }

                if (pathsData && typeof pathsData === 'object') {
                    return Object.values(pathsData);
                }

                return [];
            }
        }
    };
</script>
