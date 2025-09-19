<?php
$config = $this->module('backupmanager')->config();
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

            <?= $this->render('backupmanager:views/partials/menu.php') ?>

            <div v-if="state.view === 'list'">
                <div class="app-main">
                    <div class="app-main-container">
                        <div class="app-main-content">
                            <div class="app-main-panel">
                                <div class="app-panel-box">
                                    <div class="app-alert">
                                        <p>
                                            <strong>Внимание!</strong> В бекап будут включены:
                                        </p>
                                        <ul>
                                            <li v-for="item in activePaths"><code>{{ item.name }}</code></li>
                                        </ul>
                                        <p class="u-margin-top-small">
                                            Следующие директории и файлы будут исключены из архива:
                                        </p>
                                        <ul>
                                            <li v-for="item in excludedFolders"><code>{{ item }}</code></li>
                                        </ul>
                                    </div>

                                    <table class="cp-table">
                                        <thead>
                                        <tr>
                                            <th>Имя файла</th>
                                            <th>Размер</th>
                                            <th>Дата создания</th>
                                            <th width="150"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="!backups.length">
                                            <td class="kiss-padding-larger kiss-size-3 kiss-align-center kiss-color-muted"
                                                colspan="4">Резервные копии не найдены
                                            </td>
                                        </tr>

                                        <tr v-if="backups.length" v-for="backup in backups">
                                            <td>{{ backup.name }}</td>
                                            <td>{{ formatSize(backup.size) }}</td>
                                            <td>{{ formatDate(backup.created) }}</td>
                                            <td class="cp-table-actions">
                                                <a class="kiss-size-1"
                                                   :href="$baseUrl('/backupmanager/download?file='+backup.name)"
                                                   title="Скачать">
                                                    <icon>download</icon>
                                                </a>

                                                <a class="kiss-size-1" href="#"
                                                   @click.prevent="restoreBackup(backup.name)" title="Восстановить">
                                                    <icon>history</icon>
                                                </a>

                                                <a class="kiss-size-1" href="#"
                                                   @click.prevent="deleteBackup(backup.name)" title="Удалить">
                                                    <icon>delete</icon>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="state.view === 'progress'">
                <div class="app-main">
                    <div class="app-main-container">
                        <div class="app-main-content">
                            <div class="app-main-panel">
                                <div class="kiss-padding-larger kiss-align-center">
                                    <div class="kiss-margin-large">
                                        <app-loader size="xlarge"></app-loader>
                                    </div>
                                    <pre class="kiss-size-small kiss-color-muted kiss-text-monospace">{{ state.message }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <teleport to="body">
                <app-actionbar>
                    <div class="kiss-container" v-if="state.view === 'list'">
                        <div class="kiss-flex kiss-flex-right">
                            <button class="kiss-button kiss-button-primary" @click="createBackup" :disabled="loading">
                                {{ loading ? 'Создание...' : 'Создать резервную копию' }}
                            </button>
                        </div>
                    </div>

                    <div class="kiss-container" v-if="state.view === 'progress'">
                        <div class="kiss-flex kiss-flex-right">
                            <button class="kiss-button kiss-button-primary" @click="finishTask"
                                    :disabled="!state.finished">
                                <icon class="kiss-margin-xsmall-right" v-if="state.finished">check</icon>
                                {{ state.finished ? 'Готово' : 'Выполнение...' }}
                            </button>
                        </div>
                    </div>
                </app-actionbar>
            </teleport>
        </template>

        <script type="module">

            export default {
                data() {
                    let backups = <?=json_encode($backups)?>;
                    let settings = <?=json_encode($settings)?>;

                    return {
                        backups: backups,
                        activePaths: Object.values(settings.paths).filter(p => p._active),
                        excludedFolders: settings.exclusions,
                        loading: false,
                        state: {
                            view: 'list',
                            message: '',
                            finished: false,
                        }
                    };
                },

                mounted() {

                },

                computed: {},

                methods: {

                    runTask(url, params, initialMessage) {

                        this.state.view = 'progress';
                        this.state.message = initialMessage;
                        this.state.finished = false;

                        App.request(url, params, 'post').then(rsp => {
                            this.state.message = rsp.message || 'Задача успешно завершена!';
                        }).catch(err => {
                            this.state.message = `Ошибка: ${JSON.parse(err).error || 'Неизвестная ошибка сервера.'}`;
                        }).finally(() => {
                            this.state.finished = true;
                        });
                    },

                    finishTask() {
                        this.state.view = 'list';
                        this.state.message = '';
                        this.state.finished = false;
                        window.location.reload();
                    },

                    createBackup() {
                        this.runTask(
                            '/backupmanager/create',
                            {},
                            'Создание резервной копии... Это может занять несколько минут.'
                        );
                    },

                    restoreBackup(filename) {
                        App.ui.confirm('<strong>ЭТО ОПАСНОЕ ДЕЙСТВИЕ!</strong><br><br>Вы уверены, что хотите восстановить сайт из этого бэкапа? Все текущие файлы будут перезаписаны.', () => {
                            this.runTask(
                                '/backupmanager/restore',
                                {file: filename},
                                'Восстановление из резервной копии... Пожалуйста, не закрывайте эту вкладку.'
                            );
                        }, {
                            title: 'Подтвердите восстановление',
                            labelOk: 'Да, восстановить'
                        });
                    },

                    deleteBackup(filename) {
                        App.ui.confirm('Вы уверены, что хотите удалить этот бэкап? Это действие необратимо.', () => {
                            this.loading = true;

                            App.request('/backupmanager/delete', {file: filename}, 'post').then(rsp => {
                                App.ui.notify('Бэкап удален!', 'success');
                                window.location.reload();
                            }).catch(err => {
                                App.ui.notify(err.message || 'Ошибка при удалении', 'error');
                            }).finally(() => {
                                this.loading = false;
                            });
                        });
                    },

                    formatSize(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                    },

                    formatDate(timestamp) {
                        return new Date(timestamp * 1000).toLocaleString();
                    }
                }
            };
        </script>
    </vue-view>
</kiss-container>
