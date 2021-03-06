<template>
    <div class="recipient.index">
        <b-row>
            <b-col md="3">
                <b-button-group>
                    <button :disabled="! $auth.check(['editor', 'administrator'])" type="button" class="btn btn-primary" v-b-modal.recipient-store-modal><i class="fas fa-plus"></i></button>
                    <b-btn variant="secondary" @click="getRecipients"><i class="fas fa-sync"></i></b-btn>
                    <button :disabled="! $auth.check(['editor', 'administrator'])" type="button" class="btn btn-secondary" v-b-modal.ldap-query-modal>{{ translate('features.ldap.ldap') }}</button>
                </b-button-group>
            </b-col>

            <b-col md="4" offset="5">
                <b-form-group >
                    <b-input-group>
                        <b-form-input v-model="search" :placeholder="translate('misc.search')" @change="getRecipients"/>
                    </b-input-group>
                </b-form-group>
            </b-col>
        </b-row>

        <div v-if="!recipientsLoading">
            <b-table hover :items="recipients" :fields="fields" v-if="recipients.length" :tbody-tr-class="rowClass">
                <template v-slot:cell(app_actions)="data">
                    <button :disabled="! $auth.check(['editor', 'administrator'])" class="btn btn-warning btn-sm" @click="activation(data)"><i class="fas" :class="{ 'fa-lock': data.item.active === 1, 'fa-unlock': data.item.active === 0 }"></i></button>
                    <button :disabled="! $auth.check(['editor', 'administrator'])" class="btn btn-danger btn-sm" @click="deleteRow(data)"><i class="fas fa-trash-alt"></i></button>
                </template>
            </b-table>

            <b-alert show variant="warning" v-else>
                <h4 class="alert-heading text-center">{{ translate('misc.no-data-available') }}</h4>
            </b-alert>

            <b-row v-if="totalRows > 10">
                <b-col cols="2">
                    <b-form-select v-model="perPage" :options="displayedRowsOptions" @change="getRecipients"></b-form-select>
                </b-col>
                <b-col cols="2" offset="3">
                    <b-pagination size="md" :total-rows="totalRows" v-model="currentPage" :per-page="perPage" @change="changePage"></b-pagination>
                </b-col>

                <b-col cols="2" offset="3" v-if="recipients.length">
                    <p class="mt-1">{{ translate('misc.pagination', {'from': from, 'to': to, 'total': totalRows})}}</p>
                </b-col>
            </b-row>
        </div>

        <div class="text-center" v-if="recipientsLoading">
            <b-spinner type="grow" :label="translate('misc.loading') + '...'"></b-spinner>
        </div>

        <recipient-store
                v-on:recipient-stored="getRecipients"
        ></recipient-store>
        <query-ldap-recipients></query-ldap-recipients>
        <are-you-sure-modal v-on:answered-yes="deleteRecipient" v-on:answered-no="row = null"></are-you-sure-modal>
    </div>
</template>

<script>
    export default {
        created() {
            this.getRecipients();

            if (! window.Echo) {
                this.connectWebsocket();
            }

            window.Echo.private('task')
                .listen('Task', (e) => {
                    let task = e.task;

                    if (task.task === 'query-ldap-recipients' && task.status === 3) {
                        this.getRecipients();
                    }
                });
        },
        data() {
            return {
                /**
                 * Loader
                 */
                recipientsLoading: false,
                ldapQueryLoading: false,

                /**
                 * Pagination
                 */
                currentPage: 1,
                perPage: 10,
                totalRows: 0,
                from: 0,
                to: 0,
                displayedRowsOptions: [
                    { value: 10, text: 10 },
                    { value: 25, text: 25 },
                    { value: 50, text: 50 },
                    { value: 100, text: 100 },
                ],

                /**
                 * Search
                 */
                search: null,

                recipients: [],

                fields: [
                    {
                        key: 'payload',
                        label: this.translate('validation.attributes.payload'),
                        sortable: true,
                    },
                    {
                        key: 'data_source',
                        label: this.translate('misc.data_source'),
                        sortable: false,
                    },
                    {
                        key: 'app_actions',
                        label: this.translate('misc.options'),
                    }
                ],

                /**
                 * Are you sure modal
                 */
                row: null,
            }
        },
        methods: {
            rowClass(item, type) {
                if (!item) {
                    return;
                }

                if (item.active === 0) {
                    return 'table-secondary'
                }
            },
            activation(data) {
                if (data.item.active === 0) {
                    axios.post('/activation/' + data.item.id, {
                        model: 'RelayRecipient',
                    }).then((response) => {
                        this.$notify({
                            title: response.data.message,
                            type: 'success'
                        });

                        this.getRecipients();
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
                } else {
                    axios.patch('/activation/' + data.item.id, {
                        model: 'RelayRecipient',
                    }).then((response) => {
                        this.$notify({
                            title: response.data.message,
                            type: 'success'
                        });

                        this.getRecipients();
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
                }
            },
            deleteRow(data) {
                this.row = data.item;
                this.$bvModal.show('are-you-sure-modal');
            },
            changePage(data) {
                this.currentPage = data;
                this.getRecipients();
            },
            getRecipients() {
                this.recipientsLoading = true;

                axios.get('/recipient', {
                    params: {
                        currentPage: this.currentPage,
                        perPage: this.perPage,
                        search: this.search,
                    }
                }).then((response) => {
                    this.recipients = response.data.data.data;
                    this.totalRows = response.data.data.total;
                    this.from = response.data.data.from;
                    this.to = response.data.data.to;
                    this.recipientsLoading = false;
                }).catch((error) => {
                    let title = error.response
                        ? error.response.data.message
                        : this.translate('misc.errors.unknown');

                    this.$notify({
                        title: title,
                        type: 'error'
                    });

                    this.recipientsLoading = false;
                });
            },
            deleteRecipient() {
                axios.delete('/recipient/' + this.row.id)
                    .then((response) => {
                        this.$notify({
                            title: response.data.message,
                            type: 'success'
                        });

                        this.getRecipients();
                    }).catch((error) => {
                        let title = error.response
                            ? error.response.data.message
                            : this.translate('misc.errors.unknown');

                        this.$notify({
                            title: title,
                            type: 'error'
                        });
                    });
            }
        }
    }
</script>
