<template>
    <div class="component-wrap">

        <!-- filers -->
        <v-card color="primary" class="white--text" raised>
            <!--<v-card-title>Case Recording</v-card-title>-->
            <div class="d-flex flex-column">
                <div class="flex-grow-1 pa-2">
                    <v-text-field rounded light solo clearable append-icon="search" :loading="loading" label="Search..." v-model="filters.name"></v-text-field>
                </div>
                <div class="flex-grow-1 px-3">
                    Show Only:
                    <v-btn-toggle mandatory rounded light v-model="filter_status">
                        <v-btn small value="" :loading="loading">All</v-btn>
                        <v-btn small value="new" :loading="loading">New</v-btn>
                        <v-btn small value="onprogress" :loading="loading">On Progress</v-btn>
                        <v-btn small value="closed" :loading="loading">Closed</v-btn>
                    </v-btn-toggle>
                    <v-btn-toggle light class="float-right">
                        <v-btn :loading="loading" @click="loadFiles()" small><v-icon>mdi-refresh</v-icon></v-btn>
                    </v-btn-toggle>
                </div>
                <div class="flex-grow-1 pa-2">
                    <span v-for="(group,i) in filters.fileGroupsHolder" :key="i">
                        <v-checkbox v-bind:label="group.name" v-model="filters.fileGroupId[group.id]"></v-checkbox>
                    </span>
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
                v-bind:headers="headers"
                :options.sync="pagination"
                :search="filters.name"
                :loading="loading"
                :items="items"
                :server-items-length="totalItems"
                class="elevation-1 mytable"
                :footer-props="{
                      showFirstLastPage: true,
                'items-per-page-options': [10, 30, 50,100]
              }"
            >
        </v-data-table>
        <!-- /groups table -->

        <!-- view file dialog -->
        <v-dialog v-model="dialogs.view.show" fullscreen :laze="false" transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar class="primary">
                    <v-btn icon @click.native="dialogs.view.show = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title class="white--text">{{dialogs.view.file.name}}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark text @click.native="downloadFile(dialogs.view.file)">
                            Download
                            <v-icon right dark>file_download</v-icon></v-btn>
                    </v-toolbar-items>
                    <v-toolbar-items>
                        <v-btn dark text @click.native="trash(dialogs.view.file)">
                            Delete
                            <v-icon right dark>delete</v-icon></v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <div class="file_view_popup">
                        <div class="file_view_popup_link">
                            <v-text-field text disabled :value="getFullUrl(dialogs.view.file)"></v-text-field>
                        </div>
                        <img :src="getFullUrl(dialogs.view.file)"/>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>

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
                    { text: 'Judul',align: 'start', value: 'judul',sortable: true,filterable:true },
                    { text: 'MSISDN', value: 'msisdn_menghubungi',sortable: false,filterable:true },
                    { text: 'MSISDN', value: 'msisdn_bermasalah',  sortable: true,filterable:true },
                ],
                items: [],
                totalItems: 0,
                pagination: {
                    rowsPerPage: 10
                },

                filters: {
                    name: '',
                    selectedGroupIds: '',
                    fileGroupId: [],
                    fileGroupsHolder: []
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
            console.log('pages.files.components.FileLists.vue');

            const self = this;

            self.$eventBus.$on(['FILE_DELETED','FILE_UPLOADED'], function () {
                self.loadFiles(()=>{});
            });
        },
        watch: {

            filter_status:{
                handler () {
                }
            },
            pagination: {
                handler () {
                    this.loadFiles(()=>{});
                },
                deep: true,
            },

            'filters.fileGroupId':_.debounce(function(v) {

                let selected = [];

                _.each(v,(v,k)=>{
                    if(v) selected.push(k);
                });

                this.filters.selectedGroupIds = selected.join(',');
            },500),
            'search':_.debounce(function(){
                const self = this;
                self.loadFiles(()=>{});
            },700),
            'filters.selectedGroupIds'(v) {
                this.loadFiles(()=>{});
            },
            'filters.name':_.debounce(function(v) {
                this.loadFiles(()=>{});
            },500),
            'pagination.page':function(){
                this.loadFiles(()=>{});
            },
            'pagination.rowsPerPage':function(){
                this.loadFiles(()=>{});
            },
            items(){
                this.loading = false
                this.tableloading = false;
            }
        },
        methods: {
            getFullUrl(file, width, action) {

                let w = width || 4000;
                let act = action || 'resize';

                return LSK_APP.APP_URL +`/files/`+file.id+`/preview?w=`+w+`&action=`+act;
            },
            downloadFile(file) {
                window.open(LSK_APP.APP_URL + '/files/'+file.id+'/download?file_token='+file.file_token);
            },
            showDialog(dialog, data) {

                const self = this;

                switch (dialog){
                    case 'file_show':
                        self.dialogs.view.file = data;
                        setTimeout(()=>{
                            self.dialogs.view.show = true;
                        },500);
                        break;
                }
            },
            trash(file) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this file?",
                    okCb: ()=>{

                        axios.delete('/admin/files/' + file.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('FILE_DELETED');

                            // maybe the action took place from view file
                            // lets close it.
                            self.dialogs.view.show = false;

                        }).catch(function (error) {
                            if (error.response) {
                                self.$store.commit('showSnackbar',{
                                    message: error.response.data.message,
                                    color: 'error',
                                    duration: 3000
                                });
                            } else if (error.request) {
                                console.log(error.request);

                            } else {
                                console.log('Error', error.message);
                            }
                        });
                    },
                    cancelCb: ()=>{
                        console.log("CANCEL");
                    }
                });
            },
            loadFileGroups(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/file-groups',{params: params}).then(function(response) {
                    self.filters.fileGroupsHolder = response.data.data;
                    cb();
                });
            },
            loadFiles(cb) {
                const self = this;
                self.loading = true;
                let targetIndex = '';
                    if(self.headers.findIndex(item => item.value === self.pagination.sortBy[0] < 0)){
                        targetIndex = '';
                    }else{
                        targetIndex = self.headers.findIndex(item => item.value === self.pagination.sortBy[0]);
                    }
                const direction = self.pagination.sortDesc[0] ? 'desc' : 'asc';
                let params = {
                    "search[value]": self.filters.name,
                    file_group_id: self.filters.selectedGroupIds,
                    draw: self.pagination.page,
                    length: self.pagination.itemsPerPage,
                    start: (((self.pagination.page/self.pagination.itemsPerPage)*self.pagination.itemsPerPage)-1)*self.pagination.itemsPerPage,
                    'order[0][dir]': direction,
                    'order[0][column]' :  targetIndex
                };
                function search(nameKey, myArray){
                    for (var i=0; i < myArray.length; i++) {
                        if (myArray[i].name === nameKey) {
                            return myArray[i];
                        }
                    }
                }


                /*Object.keys(self.pagination.sortBy).find(key => self.pagination.sortBy[value] === value)*/
                $.each(self.headers, function(key, value) {
                    params['columns['+key+'][data]'] = value.value;
                    params['columns['+key+'][searchable]'] = value.filterable;
                    params['columns['+key+'][orderable]'] = value.sortable;
                });

                (this.pagination);


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
    .mytable th {
        font-weight:bolder;
    }
    .file_view_popup {
        min-width: 500px;
        text-align: center;
    }
</style>