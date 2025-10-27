<tab class="kiss-margin animated fadeIn" caption="<?= t('About') ?>" name="about">
    <table class="kiss-table">
        <tbody>
        <tr>
            <td width="30%" class="kiss-size-xsmall"><?= t('Version') ?></td>
            <td class="kiss-size-small kiss-text-monospace kiss-color-muted">{{ moduleInfo.version }}</td>
        </tr>

        <tr>
            <td width="30%" class="kiss-size-xsmall"><?= t('License') ?></td>
            <td class="kiss-size-small kiss-text-monospace kiss-color-muted">{{ moduleInfo.license }}</td>
        </tr>

        <tr>
            <td width="30%" class="kiss-size-xsmall"><?= t('GitHub repository') ?></td>
            <td class="kiss-size-small kiss-text-monospace kiss-color-muted kiss-text-truncate">
                <a class="kiss-transition"
                   :href="moduleInfo.homepage"
                   target="_blank"
                >
                    {{ moduleInfo.homepage }}
                </a>
            </td>
        </tr>

        <tr>
            <td width="30%" class="kiss-size-xsmall"><?= t('Developed by') ?></td>
            <td class="kiss-size-small kiss-text-monospace kiss-color-muted kiss-text-truncate">
                <a class="kiss-transition"
                   :href="moduleInfo.author.url"
                   target="_blank"
                >
                    {{ moduleInfo.author.url }}
                </a>
            </td>
        </tr>

        <tr>
            <td width="30%" class="kiss-size-xsmall"><?= t('Feedback and suggestions') ?></td>
            <td class="kiss-size-small kiss-text-monospace kiss-color-muted kiss-text-truncate">
                <a class="kiss-transition"
                   :href="'mailto:' + moduleInfo.author.email"
                   target="_blank"
                >
                    {{ moduleInfo.author.email }}
                </a>
            </td>
        </tr>
        </tbody>
    </table>

    <kiss-card class="kiss-padding kiss-color-muted kiss-bgcolor-contrast" theme="shadowed">
        <div class="kiss-margin-bottom">
            {{ t(moduleInfo.description) }}
        </div>

        <div class="kiss-flex kiss-flex-center">
            <a class="kiss-button kiss-button-primary kiss-bgcolor-transparent@hover kiss-color-primary@hover kiss-transition"
               :href="moduleInfo.author.support"
               target="_blank"
            >
                <?= t('Support the author') ?>
            </a>
        </div>
    </kiss-card>
</tab>
