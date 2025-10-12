<teleport to="body">
    <app-actionbar>
        <kiss-container class="kiss-flex kiss-flex-right animated fadeIn" v-if="state.view === 'list'">
            <button class="kiss-button kiss-button-primary"
                    type="button"
                    @click="createBackup"
            >
                {{ loading ? t('Creating...') : t('Create a backup') }}
            </button>
        </kiss-container>

        <kiss-container class="kiss-flex kiss-flex-right animated fadeIn" v-else-if="state.view === 'progress'">
            <button class="kiss-button kiss-button-primary"
                    type="button"
                    @click="finishTask"
            >
                {{ state.finished ? t('Done') : t('Processing...') }}
            </button>
        </kiss-container>

        <kiss-container class="kiss-flex kiss-flex-between kiss-flex-middle kiss-flex-wrap kiss-flex-center animated fadeIn"
                        v-else-if="state.view === 'settings'"
                        gap="small"
        >
            <div class="kiss-flex kiss-flex-middle" v-if="hasChanges">
                <icon class="kiss-margin-small-right kiss-color-warning">info</icon>
                <div class="kiss-color-warning">
                    <?= t('You have unsaved changes') ?>
                </div>
            </div>

            <div class="kiss-flex kiss-flex-middle kiss-margin-auto-left kiss-flex-wrap kiss-flex-center" gap="small">
                <button class="kiss-button kiss-button-outline"
                        type="button"
                        @click="resetToDefaults"
                >
                    <?= t('Reset to defaults') ?>
                </button>

                <button class="kiss-button kiss-button-primary"
                        type="button" @click="saveSettings"
                        :disabled="!hasChanges"
                >
                    {{ loading ? t('Saving...') : t('Save settings') }}
                </button>
            </div>
        </kiss-container>
    </app-actionbar>
</teleport>
