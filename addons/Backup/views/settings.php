    <?php
    $settings = $this->module('backup')->getSettings();
    ?>

    <kiss-container class="kiss-margin-small" size="medium">
        <ul class="kiss-breadcrumbs">
            <li><a href="<?= $this->route('/system') ?>"><?= t('Settings') ?></a></li>
        </ul>

        <vue-view>
            <template>
                <div class="kiss-margin-large-bottom kiss-size-3 kiss-text-bold">
                    <?= t('Backup Settings') ?>
                </div>

                <?= $this->render('backup:views/partials/menu.php', ['view' => 'settings']) ?>

                <div class="kiss-margin-bottom animated fadeIn">
                    <div class="kiss-margin-small-bottom">
                        <div class="kiss-flex kiss-flex-middle">
                            <icon class="kiss-margin-small-right kiss-color-primary">checklist</icon>
                            <strong><?= t('Backup configuration') ?></strong>
                        </div>

                        <div class="kiss-text-caption kiss-color-muted">
                            <?= t('Select which parts of your system should be included in the backup') ?>
                        </div>
                    </div>

                    <div class="kiss-flex kiss-flex-column" gap="small">
                        <label class="kiss-card kiss-color-primary@hover kiss-padding-small kiss-bgcolor-contrast kiss-flex kiss-flex-middle kiss-cursor-pointer"
                            v-for="path in paths"
                            :key="path.code">
                            <input type="checkbox" v-model="path._active" name="path" hidden>

                            <div class="kiss-flex kiss-flex-middle kiss-flex-1">
                                <kiss-svg :src="$baseUrl(path.icon)" width="24" height="24"
                                          class="kiss-margin-small-right"></kiss-svg>
                                <div>
                                    <div class="kiss-text-bold">{{ path.name }}</div>
                                    <div class="kiss-size-small kiss-color-muted">{{ path.description }}</div>
                                </div>
                            </div>

                            <div class="kiss-badge"
                                 :class="path._active ? 'kiss-badge-success' : 'kiss-badge-outline'">
                                {{ path._active ? t('Included') : t('Excluded') }}
                            </div>
                        </label>
                    </div>
                </div>

                <div class="animated fadeIn">
                    <div class="kiss-margin-small-bottom">
                        <div class="kiss-flex kiss-flex-middle">
                            <icon class="kiss-margin-small-right kiss-color-warning">block</icon>
                            <strong><?= t('Excluded paths') ?></strong>
                        </div>

                        <div class="kiss-text-caption kiss-color-muted">
                            <?= t('Paths that will be excluded from the backup. Specify relative to the site root directory.') ?>
                        </div>
                    </div>

                    <div class="kiss-card kiss-padding kiss-bgcolor-contrast">
                        <div class="kiss-margin-bottom">
                            <div class="kiss-grid kiss-child-width-1-1 kiss-child-width-1-2@m" gap="small">
                                <div v-for="(exclusion, index) in exclusions" :key="exclusion.id"
                                     class="kiss-flex kiss-flex-middle kiss-margin-small-bottom">
                                    <div class="kiss-input-group kiss-flex-1">
                                        <input class="kiss-input"
                                               type="text"
                                               v-model="exclusion.value"
                                               :placeholder="t('Path to folder or file')"
                                               @keyup.enter="saveSettings">
                                    </div>

                                    <button
                                        class="kiss-button kiss-button-small kiss-button-danger kiss-margin-small-left"
                                        @click="removeExclusion(index)"
                                        :title="t('Remove exclusion')">
                                        <icon>close</icon>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="kiss-flex kiss-flex-center">
                            <button class="kiss-button kiss-button-outline" @click="addExclusion">
                                <icon class="kiss-margin-small-right">add</icon>
                                {{ t('Add exclusion') }}
                            </button>
                        </div>
                    </div>
                </div>

                <teleport to="body">
                    <app-actionbar>
                        <div class="kiss-container">
                            <div class="kiss-flex kiss-flex-between kiss-flex-middle">
                                <div class="kiss-flex kiss-flex-middle">
                                    <div v-if="hasChanges">
                                        <icon class="kiss-margin-small-right kiss-color-warning">info</icon>
                                        <span
                                            class="kiss-size-small kiss-color-warning"><?= t('You have unsaved changes') ?></span>
                                    </div>

                                    <div v-else>
                                        <icon class="kiss-margin-small-right kiss-color-success">done</icon>
                                        <span
                                            class="kiss-size-small kiss-color-success"><?= t('All settings are saved') ?></span>
                                    </div>
                                </div>

                                <div class="kiss-flex kiss-flex-middle" gap="small">
                                    <button class="kiss-button kiss-button-outline"
                                            @click="resetToDefaults"
                                            :disabled="loading">
                                        <?= t('Reset to defaults') ?>
                                    </button>

                                    <button class="kiss-button kiss-button-primary"
                                            @click="saveSettings"
                                            :disabled="loading || !hasChanges">
                                        {{ loading ? t('Saving...') : t('Save settings') }}
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
                        const settings = <?= json_encode($settings) ?>;
                        let nextId = 0;

                        return {
                            loading: false,
                            paths: Object.values(settings.paths),
                            exclusions: (settings.exclusions || []).map(value => ({
                                id: nextId++,
                                value: value
                            })),
                            nextExclusionId: nextId,
                            originalData: null
                        };
                    },

                    mounted() {
                        this.originalData = this.getCurrentData();
                    },

                    computed: {
                        hasChanges() {
                            return JSON.stringify(this.getCurrentData()) !== JSON.stringify(this.originalData);
                        },

                        activeSectionsCount() {
                            return this.paths.filter(p => p._active).length;
                        }
                    },

                    methods: {
                        getCurrentData() {
                            try {
                                return {
                                    paths: (Array.isArray(this.paths) ? this.paths : []).map(p => ({
                                        code: p && p.code ? String(p.code) : '',
                                        _active: Boolean(p && p._active)
                                    })),
                                    exclusions: (Array.isArray(this.exclusions) ? this.exclusions : [])
                                        .map(e => e && e.value ? String(e.value) : '')
                                        .filter(v => v.trim().length > 0)
                                };
                            }
                            catch (error) {
                                console.error('Error in getCurrentData:', error);
                                return {paths: [], exclusions: []};
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
                            if (this.exclusions.length > 1) {
                                this.exclusions.splice(index, 1);
                            } else {
                                this.exclusions[0].value = '';
                            }
                        },

                        async resetToDefaults() {
                            try {
                                const confirmed = await new Promise((resolve) => {
                                    App.ui.confirm(
                                        App.i18n.get('Are you sure you want to reset all settings to default values?'),
                                        () => resolve(true),
                                        () => resolve(false),
                                        {
                                            title: App.i18n.get('Confirm Reset'),
                                            labelOk: App.i18n.get('Yes, Reset'),
                                            labelCancel: App.i18n.get('Cancel')
                                        }
                                    );
                                });

                                if (confirmed) {
                                    this.paths.forEach(path => {
                                        path._active = path.code === 'Core' || path.code === 'Database';
                                    });

                                    this.exclusions = [];
                                    this.nextExclusionId = 0;

                                    App.ui.notify(App.i18n.get('Settings reset to defaults'), 'success');
                                }
                            }
                            catch (error) {
                                console.error('Reset error:', error);
                            }
                        },

                        async saveSettings() {
                            if (!this.hasChanges) {
                                App.ui.notify(App.i18n.get('No changes to save'), 'info');
                                return;
                            }

                            this.loading = true;

                            try {
                                const payload = {
                                    inclusions: this.paths.filter(path => path._active).map(path => path.code),
                                    exclusions: this.exclusions
                                        .map(item => item.value.trim())
                                        .filter(value => value)
                                };

                                if (payload.inclusions.length === 0) {
                                    throw new Error(App.i18n.get('Please select at least one section to include in the backup'));
                                }

                                const response = await App.request('/backup/save', payload, 'post');

                                this.originalData = this.getCurrentData();

                                App.ui.notify(App.i18n.get('Settings saved successfully!'), 'success');

                            }
                            catch (error) {
                                App.ui.notify(
                                    error.message || App.i18n.get('Failed to save settings'),
                                    'error'
                                );
                            }
                            finally {
                                this.loading = false;
                            }
                        }
                    },

                    beforeUnmount() {
                        if (this.hasChanges) {
                            return App.i18n.get('You have unsaved changes. Are you sure you want to leave?');
                        }
                    }
                };
            </script>
        </vue-view>
    </kiss-container>
