<?php
$settings = $this->module('backupmanager')->getSettings();
?>
<kiss-container class="kiss-margin-small" size="medium">
    <ul class="kiss-breadcrumbs">
        <li><a href="<?= $this->route('/system') ?>"><?= t('Settings') ?></a></li>
    </ul>

    <vue-view>
        <template>
            <div class="kiss-margin-large-bottom kiss-size-3 kiss-text-bold">
                <?= t('Backup Manager') ?>
            </div>

            <?= $this->render('backupmanager:views/partials/menu.php', ['view' => 'settings']) ?>

            <div class="kiss-margin-large">
                <div class="kiss-text-caption kiss-margin">{{ t('Parts to include in backup') }}</div>
                <kiss-card class="kiss-padding kiss-flex kiss-flex-middle kiss-margin-xsmall kiss-cursor-pointer"
                           :theme="path._active ? 'bordered shadowed contrast' : 'contrast'" hover="bordered-primary"
                           v-for="path in paths" :key="path.name"
                           @click="path._active = !path._active">
                    <kiss-svg class="kiss-color-muted" :src="$baseUrl(path.icon)" width="25" height="25"></kiss-svg>

                    <div class="kiss-text-capitalize kiss-flex-1 kiss-margin-small-left">{{ path.name }} <span
                            class="kiss-color-muted kiss-size-small">- {{path.description}}</span></div>

                    <a class="kiss-size-1" :class="path._active ? 'kiss-color-success' : 'kiss-color-muted'">
                        <icon>{{ path._active ? 'toggle_on' : 'toggle_off' }}</icon>
                    </a>
                </kiss-card>
            </div>

            <div class="kiss-margin-large">
                <label class="kiss-text-caption">{{ t('Paths to exclude from backup') }}</label>

                <div class="kiss-size-small kiss-color-muted kiss-margin-small-top">
                    {{ t('Paths are relative to the project root.') }}
                </div>

                <div class="kiss-margin-small-top">
                    <div class="kiss-color-muted kiss-size-small" v-if="!exclusions.length">
                        {{ t('No exclusions defined.') }}
                    </div>

                    <div class="kiss-flex kiss-flex-middle kiss-margin-xsmall-bottom"
                         v-for="(exclusion, index) in exclusions" :key="exclusion.id">
                        <input class="kiss-input kiss-flex-1" type="text" v-model="exclusion.value"
                               :placeholder="t('e.g., dist or .env.local')">

                        <a class="kiss-margin-small-left kiss-size-3 kiss-color-danger kiss-cursor-pointer"
                           @click="removeExclusion(index)" :title="t('Remove')">
                            <icon>remove_circle</icon>
                        </a>
                    </div>
                </div>

                <div class="kiss-margin-top">
                    <button class="kiss-button" @click.prevent="addExclusion">
                        <icon class="kiss-margin-small-right">add</icon>
                        {{ t('Add exclusion') }}
                    </button>
                </div>
            </div>

            <teleport to="body">
                <app-actionbar>
                    <div class="kiss-container">
                        <div class="kiss-flex kiss-flex-right">
                            <button class="kiss-button kiss-button-primary" @click="saveSettings" :disabled="loading">
                                {{ loading ? 'Сохранение...' : 'Сохранить настройки' }}
                            </button>
                        </div>
                    </div>
                </app-actionbar>
            </teleport>
        </template>

        <script type="module">
            export default {
                data() {
                    let settings = <?=json_encode($settings)?>;
                    let nextId = 0;

                    return {
                        loading: false,
                        paths: Object.values(settings.paths),

                        exclusions: (settings.exclusions || []).map(value => ({
                            id: nextId++,
                            value: value
                        })),

                        nextExclusionId: nextId
                    };
                },
                methods: {
                    addExclusion() {
                        this.exclusions.push({
                            id: this.nextExclusionId++,
                            value: ''
                        });
                    },

                    removeExclusion(index) {
                        this.exclusions.splice(index, 1);
                    },

                    saveSettings() {
                        this.loading = true;

                        const payload = {
                            inclusions: this.paths.filter(p => p._active).map(p => p.name),

                            exclusions: this.exclusions
                                .map(item => item.value.trim())
                                .filter(value => value)
                        };

                        App.request('/backupmanager/save', payload, 'post').then(rsp => {
                            App.ui.notify('Настройки сохранены!', 'success');
                        }).catch(err => {
                            App.ui.notify('Ошибка сохранения', 'error');
                        }).finally(() => {
                            this.loading = false;
                        });

                    }
                }
            };
        </script>
    </vue-view>
</kiss-container>
