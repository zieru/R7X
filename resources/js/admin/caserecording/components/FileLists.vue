<template>
    <div>
        <v-card v-cloak color="primary" class="white--text" raised>
            <v-card-title>Case Recording</v-card-title>
            <div class="d-flex flex-column">
                <div class="flex-grow-1 pa-2">
                    <v-text-field rounded light solo clearable prepend-inner-icon="search" hint="Enter Keyword" :loading="loading" label="Search..." v-model="filters.name"></v-text-field>
                </div>
                <div class="flex-grow-1 pa-3">
                    Show Only:
                    <v-btn-toggle mandatory rounded light v-model="filter_status">
                        <v-btn small value="" :loading="loading">All</v-btn>
                        <v-btn small value="new" :loading="loading">New</v-btn>
                        <v-btn small value="uploaded" :loading="loading">Uploaded</v-btn>
                        <v-btn small value="onprogress" :loading="loading">On Progress</v-btn>
                        <v-btn small value="closed" :loading="loading">Closed</v-btn>
                    </v-btn-toggle>
                    <v-btn-toggle light class="float-right">
                        <v-btn :loading="loading" @click="loadFiles()" small><v-icon>mdi-refresh</v-icon></v-btn>
                    </v-btn-toggle>
                </div>
            </div>
        </v-card>


                <v-skeleton-loader
                    v-if="tableloading == true"
                    :loading="tableloading"
                    type="table"
                    tile="tile"
                    class="mx-auto"
                ></v-skeleton-loader>


        <!-- groups table -->

        <v-data-table
                v-cloak
                fixed-header
                height="480px"
                v-bind:headers="headers"
                :options.sync="pagination"
                :search="filters.name"
                :loading="loading"
                :items="items"
                :server-items-length="totalItems"
                class="elevation-1 mytable"
                :footer-props="{
                      showFirstLastPage: true,
                'items-per-page-options': [30, 50,100,500,1000]
              }"
            >
            <template v-slot:body="{items}" v-cloak>
                <tbody>
                <tr v-for="item in items" :key="item.id_laporan">
                    <td>
                        <v-btn
                                small
                                transition="slide-y-transition"
                                @click="$router.push({name:'caserecording.view',params:{id: item.id_laporan}})"
                        >View
                        </v-btn>
                    </td>
                    <td>{{ item.judul }}</td>
                    <td>{{ item.tipe_layanan }}</td>
                    <td>{{ item.ket }}</td>
                    <td>{{ item.msisdn_bermasalah }} / {{ item.msisdn_menghubungi }}</td>
                    <td><v-icon>mdi-account-circle</v-icon> {{ item.id_co || 'n/a' }}</td>
                    <td><v-icon>mdi-clock</v-icon> {{ $appFormatters.formatDatetimeago(item.created_at) }}</td>
                </tr>
                </tbody>
            </template>
        </v-data-table>
        <!-- /groups table -->
    </div>
</template>

<script>
    export default {
        computed: {
            cssProps() {
                return {
                    '--primary-color': this.$vuetify.theme.dark
                }
            },
        },
        components: {},
        data() {
            return {
                filter_status: null,
                loading:true,
                tableloading:true,
                headers: [
                    { text: 'Action',align: 'start', value: 'id',sortable: false,filterable:false },
                    { text: 'Judul',align: 'start', value: 'judul',sortable: true,filterable:true },
                    { text: 'Site',align: 'start', value: 'tipe_layanan',sortable: true,filterable:true },
                    { text: 'Status',align: 'start', value: 'ket',sortable: true,filterable:true },
                    { text: 'MSISDN', value: 'msisdn_menghubungi',sortable: true,filterable:true },
                    { text: 'Agent', value: 'id_co',sortable: false,filterable:true },
                    { text: 'Last Date', value: 'updated_at',sortable: true,filterable:true }

                ],
                items: [],
                totalItems: 0,
                pagination: {
                    sortBy: ["updated_at"],
                    sortDesc: [true],
                },

                filters: {
                    name: '',
                },

                dialogs: {
                    view: {
                        file: {},
                        show: false
                    },
                }
            }
        },
        mounted() {

            const self = this;

            self.$eventBus.$on(['FILE_DELETED','FILE_UPLOADED'], function () {
                self.loadFiles(()=>{});
            });
        },
        watch: {

            filter_status:{
                handler () {
                    const self = this;
                    self.loadFiles(()=>{});
                }
            },
            pagination: {
                handler () {
                    this.loadFiles(()=>{});
                },
                deep: true,
            },
            'filters.name':_.debounce(function(v) {
                this.loadFiles(()=>{});
            },500),
            items(){
                this.loading = false
                this.tableloading = false;
            }
        },
        methods: {
            loadFiles(cb) {
                const self = this;
                self.loading = true;

                let filter_status = null;
                if (self.filter_status) filter_status = self.filter_status;

                let targetIndex = '';
                (Math.sign(self.headers.findIndex(item => item.value === self.pagination.sortBy[0]) <= 0)) ? targetIndex = '' : targetIndex = self.headers.findIndex(item => item.value === self.pagination.sortBy[0])

                const direction = self.pagination.sortDesc[0] ? 'desc' : 'asc';
                let params = {
                    "search[value]": self.filters.name,
                    status: filter_status,
                    draw: self.pagination.page,
                    length: self.pagination.itemsPerPage,
                    start: (((self.pagination.page/self.pagination.itemsPerPage)*self.pagination.itemsPerPage)-1)*self.pagination.itemsPerPage,
                    'order[0][dir]': direction,
                    'order[0][column]' :  targetIndex
                };

                $.each(self.headers, function(key, value) {
                    params['columns['+key+'][data]'] = value.value;
                    params['columns['+key+'][searchable]'] = value.filterable;
                    params['columns['+key+'][orderable]'] = value.sortable;
                });




                axios.get('/api/admin/caserecording',{params: params})
                    .then(function(response) {
                        self.items = response.data.data;
                        self.totalItems = response.data.recordsFiltered;
                        self.pagination.totalItems = self.totalItems;
                        (cb || Function)();

                    }).catch(function(error) {
                        console.log(error.response);
                    if (error.response && error.response.status != 200) {
                        self.$store.commit('showSnackbar', {
                            message: error.response.status+':'+ error.response.statusText + ' Please Reload Your Browser',
                            color: 'error',
                            duration: 10000
                        });
                    };
                        self.loading = false;
                        self.tableloading = false;
                })
            }
        }
    }
</script>

<style scoped>

    .file_view_popup {
        min-width: 500px;
        text-align: center;
    }
</style>