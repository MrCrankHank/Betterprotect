<template>
    <div class="server.index">
        <b-row class="mb-2">
            <b-col md="1">
                <b-button-group>
                    <b-btn :disabled="! $auth.check(['editor', 'administrator'])" variant="primary" v-b-modal.server-wizard><i class="fas fa-plus"></i></b-btn>
                    <b-btn variant="secondary" @click="getAllServers"><i class="fas fa-sync"></i></b-btn>
                </b-button-group>
            </b-col>
        </b-row>

        <b-modal id="server-wizard" size="xl" :title="translate('features.server.wizard')" :hide-footer="true" :no-close-on-backdrop="true">
            <form-wizard color="#007bff" :key="serverWizardKey">
                <h2 slot="title"></h2>

                <tab-content title="Server" icon="fas fa-server" >
                    <server-wizard-server-form :bus="bus" @server-wizard-submit-server-success="updateServerWizardId"></server-wizard-server-form>
                </tab-content>
                <tab-content title="Postfix" icon="fas fa-envelope">
                    <server-wizard-postfix-form :bus="bus" :server="server"></server-wizard-postfix-form>
                </tab-content>
                <tab-content title="Konsole" icon="fas fa-terminal">
                    <server-wizard-console-form :bus="bus" :server="server"></server-wizard-console-form>
                </tab-content>
                <tab-content title="Logging" icon="fas fa-tasks">
                    <server-wizard-logging-form :bus="bus" :server="server"></server-wizard-logging-form>
                </tab-content>

                <template slot="footer" slot-scope="props">
                    <div class="wizard-footer-left">
                        <wizard-button  v-if="props.activeTabIndex > 0" @click.native="props.prevTab()" :style="props.fillButtonStyle">{{ translate('misc.back') }}</wizard-button>
                    </div>
                    <div class="wizard-footer-right">
                        <wizard-button v-if="!props.isLastStep" @click.native="wizardButtonNext(props)" class="wizard-footer-right" :style="props.fillButtonStyle">{{ translate('misc.next') }}</wizard-button>

                        <wizard-button v-else @click.native="wizardButtonNext(props)" class="wizard-footer-right finish-button" :style="props.fillButtonStyle">  {{ props.isLastStep ? translate('misc.finish') : translate('misc.next') }}</wizard-button>
                    </div>
                </template>
            </form-wizard>
        </b-modal>

        <b-modal id="server-edit" size="xl" title="Server bearbeiten" :hide-footer="true" :no-close-on-backdrop="true">
            <b-card>
                <b-tabs content-class="mt-3" fill>
                    <b-tab :title="translate('features.server.title')" active>
                        <server-update-server-form :server="server" @edit-server-finished="hideEditModal"></server-update-server-form>
                    </b-tab>
                    <b-tab :title="translate('features.postfix.title')">
                        <server-update-postfix-form :server="server" @edit-server-finished="hideEditModal"></server-update-postfix-form>
                    </b-tab>
                    <b-tab :title="translate('features.console.title')">
                        <server-update-console-form :server="server" @edit-server-finished="hideEditModal"></server-update-console-form>
                    </b-tab>
                    <b-tab :title="translate('features.logging.title')">
                        <server-update-logging-form :server="server" @edit-server-finished="hideEditModal"></server-update-logging-form>
                    </b-tab>
                </b-tabs>
            </b-card>
        </b-modal>

        <server
                v-if="!loading"
                v-for="item in servers"
                v-bind:server="item"
                v-bind:key="item.id"
                v-on:server-deleted="getAllServers"
                v-on:edit-server="openEditModal"
        ></server>

        <b-alert show variant="warning" v-if="!servers.length && !loading">
            <h4 class="alert-heading text-center">{{ translate('misc.no-data-available') }}</h4>
        </b-alert>

        <div class="text-center" v-if="loading">
            <b-spinner type="grow" :label="translate('misc.loading') + '...'"></b-spinner>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import axios from 'axios'

    export default {
        data() {
            return {
                servers: [],

                /**
                 * Wizard
                 */
                bus: new Vue(),
                server: null,
                serverWizardKey: 0,

                /**
                 * Loader
                 */
                loading: false,
            }
        },
        created() {
            this.getAllServers();

            /*
             * Rerender wizard due to a bug which reorders the items.
             */
            this.$root.$on('bv::modal::show', (bvEvent, modalId) => {
                this.serverWizardKey += 1;
            });
        },
        methods: {
            /**
             * Wizard
             */
            wizardButtonNext(props) {
                switch (props.activeTabIndex) {
                    case 0:
                        this.bus.$emit('server-wizard-submit-server', props);
                        break;
                    case 1:
                        this.bus.$emit('server-wizard-submit-postfix', props);
                        break;
                    case 2:
                        this.bus.$emit('server-wizard-submit-console', props);
                        break;
                    case 3:
                        this.bus.$emit('server-wizard-submit-log', props);
                        break;
                }

                if (props.isLastStep) {
                    this.$bvModal.hide('server-wizard');

                    this.$notify({
                        title: this.translate('features.server.configuration-finished'),
                        type: 'success'
                    });

                    this.getAllServers();
                }
            },
            updateServerWizardId(data) {
                this.server = data;
            },
            getAllServers() {
                this.loading = true;
                axios.get('/server').then((response) => {
                    this.servers = response.data;

                    this.loading = false;
                }).catch((error) => {
                    let title = error.response
                        ? error.response.data.message
                        : this.translate('misc.errors.unknown');

                    this.$notify({
                        title: title,
                        type: 'error'
                    });

                    this.loading = false;
                });
            },
            openEditModal(server) {
                this.server = server;

                this.$bvModal.show('server-edit');
            },
            hideEditModal() {
                this.$bvModal.hide('server-edit');

                this.getAllServers();
            }
        }
    }
</script>

<style>
    .vue-form-wizard .wizard-icon {
        height: inherit;
    }
</style>
