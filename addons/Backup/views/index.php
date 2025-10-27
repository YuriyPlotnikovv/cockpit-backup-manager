<kiss-container class="kiss-margin-small" size="medium">
    <ul class="kiss-breadcrumbs">
        <li>
            <a href="<?= $this->route('/system') ?>">
                <?= t('Settings') ?>
            </a>
        </li>
    </ul>

    <vue-view>
        <template>
            <div class="kiss-margin-large-bottom kiss-size-3 kiss-text-bold">
                <?= t('Backup') ?>
            </div>

            <kiss-tabs @click="handleTabClick" v-if="state.view !== 'progress'">
                <?= $this->render('backup:views/parts/tab-list.php') ?>

                <?= $this->render('backup:views/parts/tab-settings.php') ?>

                <?= $this->render('backup:views/parts/tab-about.php') ?>
            </kiss-tabs>

            <?= $this->render('backup:views/parts/state-progress.php') ?>

            <?= $this->render('backup:views/parts/action-bar.php') ?>
        </template>

        <?= $this->render('backup:views/parts/script.php') ?>
    </vue-view>
</kiss-container>
