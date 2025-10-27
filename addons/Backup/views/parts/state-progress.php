<div class="kiss-padding-larger kiss-align-center animated fadeIn" v-if="state.view === 'progress'">
    <div class="kiss-margin">
        <app-loader v-if="!state.isFinished"></app-loader>

        <icon class="kiss-color-danger" size="large" v-else-if="state.isFinished && state.isError">error</icon>

        <icon class="kiss-color-success" size="large" v-else-if="state.isFinished && !state.isError">check_circle</icon>
    </div>

    <div class="kiss-size-large kiss-margin-small-bottom">
        {{ state.message }}
    </div>

    <div class="kiss-size-small kiss-color-muted" v-if="state.details">
        {{ state.details }}
    </div>
</div>
