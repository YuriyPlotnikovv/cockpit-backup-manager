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
        <label
            class="kiss-padding kiss-bgcolor-contrast kiss-flex kiss-flex-middle kiss-cursor-pointer kiss-color-primary@hover kiss-transition"
            v-for="path in paths"
            :key="path.code"
        >
            <input type="checkbox" v-model="path.active" name="path" hidden>

            <div class="kiss-flex kiss-flex-middle kiss-flex-1">
                <kiss-svg :src="$baseUrl(path.icon)" width="24" height="24" class="kiss-margin-small-right"></kiss-svg>

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

    <ul class="kiss-flex kiss-flex-column kiss-margin-bottom kiss-padding-remove-left" gap="small">
        <li class="kiss-flex kiss-flex-middle kiss-padding kiss-bgcolor-contrast animated fadeIn"
            v-for="(exclusion, index) in exclusions"
            :key="exclusion.id"
            gap="medium"
        >
            <input class="kiss-input kiss-input-small kiss-flex-1 kiss-text-monospace"
                   type="text"
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
        </li>
    </ul>

    <div class="kiss-flex kiss-flex-center">
        <button class="kiss-button kiss-button-outline"
                type="button"
                @click="addExclusion"
        >
            <icon class="kiss-margin-small-right">add</icon>

            {{ t('Add exclusion') }}
        </button>
    </div>
</tab>
