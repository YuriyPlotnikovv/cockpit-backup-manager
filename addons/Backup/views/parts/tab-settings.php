<tab class="kiss-margin animated fadeIn" caption="<?= t('Backup settings') ?>" name="settings">
    <div class="kiss-margin-bottom">
        <div class="kiss-flex kiss-flex-middle kiss-margin-small-bottom">
            <icon class="kiss-margin-small-right kiss-color-primary">checklist</icon>

            <div class="kiss-text-bold">
                <?= t('Backup configuration') ?>
            </div>
        </div>

        <div class="kiss-text-caption kiss-color-muted">
            <?= t('Select which parts of your system should be included in the backup') ?>
        </div>
    </div>

    <div class="kiss-flex kiss-flex-column kiss-margin-large-bottom" gap="small">
        <kiss-card theme="shadowed" v-for="path in paths" :key="path.code">
            <label
                class="kiss-padding kiss-margin-remove-bottom kiss-bgcolor-contrast kiss-flex kiss-flex-middle kiss-cursor-pointer kiss-color-primary@hover kiss-transition">
                <input type="checkbox" v-model="path.active" name="path" hidden>

                <div class="kiss-flex kiss-flex-middle kiss-flex-1">
                    <kiss-svg :src="$baseUrl(path.icon)" width="24" height="24"
                              class="kiss-margin-small-right"></kiss-svg>

                    <div>
                        <div class="kiss-text-bold">
                            {{ path.name }}
                        </div>

                        <div class="kiss-size-small kiss-color-muted">
                            {{ path.description }}
                        </div>
                    </div>
                </div>

                <div class="kiss-badge kiss-size-xsmall kiss-text-leading-relaxed kiss-transition"
                     :class="path.active ? '' : 'kiss-badge-outline'"
                >
                    {{ path.active ? t('Included') : t('Excluded') }}
                </div>
            </label>
        </kiss-card>
    </div>

    <div class="kiss-margin-bottom">
        <div class="kiss-flex kiss-flex-middle kiss-margin-small-bottom">
            <icon class="kiss-margin-small-right kiss-color-warning">block</icon>

            <div class="kiss-text-bold">
                <?= t('Excluded paths') ?>
            </div>
        </div>

        <div class="kiss-text-caption kiss-color-muted">
            <?= t('Paths that will be excluded from the backup. Specify relative to the site root directory.') ?>
        </div>
    </div>

    <div class="kiss-flex kiss-flex-column kiss-margin-bottom kiss-padding-remove-left" gap="small">
        <kiss-card class="kiss-flex kiss-flex-middle kiss-padding kiss-bgcolor-contrast animated fadeIn"
                   v-for="(exclusion, index) in exclusions"
                   :key="exclusion.id"
                   gap="medium"
                   theme="shadowed"
        >
            <input class="kiss-input kiss-input-small kiss-flex-1 kiss-text-monospace"
                   type="text"
                   name="exclusion"
                   v-model="exclusion.value"
                   :placeholder="t('Path to folder or file')"
                   @keyup.enter="addExclusion"
            >

            <button class="kiss-button kiss-button-small kiss-button-danger"
                    type="button"
                    @click="removeExclusion(index)"
                    :title="t('Remove exclusion')"
            >
                <icon>close</icon>
            </button>
        </kiss-card>
    </div>

    <div class="kiss-flex kiss-flex-center">
        <button class="kiss-button kiss-button-outline"
                type="button"
                @click="addExclusion"
        >
            <icon class="kiss-margin-small-right">add</icon>

            {{ t('Add exclusion') }}
        </button>
    </div>

    <div class="kiss-margin-large-top" v-if="isMongoDB">
        <div class="kiss-margin-bottom">
            <div class="kiss-flex kiss-flex-middle kiss-margin-small-bottom">
                <icon class="kiss-margin-small-right kiss-color-info kiss-color-primary">database</icon>

                <div class="kiss-text-bold">
                    <?= t('MongoDB Tools Paths') ?>
                </div>
            </div>

            <div class="kiss-text-caption kiss-color-muted">
                <?= t('Specify absolute paths to MongoDB tools if they are not in the system\'s PATH.') ?>
            </div>
        </div>

        <div class="kiss-flex kiss-flex-column" gap="small">
            <kiss-card class="kiss-padding kiss-bgcolor-contrast kiss-flex kiss-flex-middle kiss-flex-between"
                       theme="shadowed"
                       gap="medium"
            >
                <div class="kiss-size-small kiss-color-muted kiss-text-bold" style="min-width: 100px;">
                    mongosh
                </div>

                <input class="kiss-input kiss-input-small kiss-flex-1 kiss-text-monospace"
                       type="text"
                       name="mongosh"
                       v-model="mongoshPath"
                       :placeholder="t('The absolute path to the executable file')"
                >

                <div class="kiss-badge kiss-size-xsmall kiss-text-leading-relaxed kiss-transition"
                     :class="mongoToolsStatus.mongosh_available ? 'kiss-bgcolor-success' : 'kiss-bgcolor-danger'"
                >
                    {{ mongoToolsStatus.mongosh_available ? t('Available') : t('Not available') }}
                </div>
            </kiss-card>

            <kiss-card class="kiss-padding kiss-bgcolor-contrast kiss-flex kiss-flex-middle kiss-flex-between"
                       theme="shadowed"
                       gap="medium"
            >
                <div class="kiss-size-small kiss-color-muted kiss-text-bold" style="min-width: 100px;">
                    mongodump
                </div>

                <input class="kiss-input kiss-input-small kiss-flex-1 kiss-text-monospace"
                       type="text"
                       name="mongodump"
                       v-model="mongodumpPath"
                       :placeholder="t('The absolute path to the executable file')"
                >

                <div class="kiss-badge kiss-size-xsmall kiss-text-leading-relaxed kiss-transition"
                     :class="mongoToolsStatus.mongodump_available ? 'kiss-bgcolor-success' : 'kiss-bgcolor-danger'"
                >
                    {{ mongoToolsStatus.mongodump_available ? t('Available') : t('Not available') }}
                </div>
            </kiss-card>

            <kiss-card class="kiss-padding kiss-bgcolor-contrast kiss-flex kiss-flex-middle kiss-flex-between"
                       theme="shadowed"
                       gap="medium"
            >
                <div class="kiss-size-small kiss-color-muted kiss-text-bold" style="min-width: 100px;">
                    mongorestore
                </div>

                <input class="kiss-input kiss-input-small kiss-flex-1 kiss-text-monospace"
                       type="text"
                       name="mongorestore"
                       v-model="mongorestorePath"
                       :placeholder="t('The absolute path to the executable file')"
                >

                <div class="kiss-badge kiss-size-xsmall kiss-text-leading-relaxed kiss-transition"
                     :class="mongoToolsStatus.mongorestore_available ? 'kiss-bgcolor-success' : 'kiss-bgcolor-danger'"
                >
                    {{ mongoToolsStatus.mongorestore_available ? t('Available') : t('Not available') }}
                </div>
            </kiss-card>
        </div>
    </div>
</tab>
