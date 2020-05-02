<template>
    <div>
        <v-row>
            <v-card style="width:100%;" tile :loading="LoadingCase">
            <v-toolbar flat>
                <v-btn icon class="hidden-xs-only">
                    <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
                <v-toolbar-title><b>{{ CaseTitle }}</b></v-toolbar-title>

                <v-spacer></v-spacer>
            </v-toolbar>
                <v-skeleton-loader
                        type="list-item-three-line"
                        class="mx-auto"
                        v-if="LoadingCase"
                ></v-skeleton-loader>
                <v-card-text class="mx-auto">
                    {{ CaseDesc }}
                </v-card-text>


            </v-card>
        </v-row>
        <v-row>
            <v-col md="6">
                <v-list two-line dense v-cloak>
                    <v-row>
                    <v-col md="6">
                            <v-list-item v-for="(Case, i) in CaseDetails" md="6" v-if="i < 3"
                                         :key="i">
                                <v-list-item-content>
                                    <v-list-item-title><b>{{ Case.name }}</b></v-list-item-title>
                                    <v-list-item-subtitle>{{ Case.val }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                    </v-col>
                    <v-col md="6">
                        <v-list-item v-for="(Case, i) in CaseDetails" v-if="i >= 3" md="6"
                                     :key="i">
                            <v-list-item-content>
                                <v-list-item-title><b>{{ Case.name }}</b></v-list-item-title>
                                <v-list-item-subtitle>{{ Case.val }}</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-col>

                    </v-row>
                </v-list>
                <v-skeleton-loader
                        v-if="LoadingCase"
                        type="card"
                        class="mx-auto"
                ></v-skeleton-loader>
                <v-skeleton-loader
                        type="card"
                        class="mx-auto"
                ></v-skeleton-loader>
            </v-col>
            <v-col md="6">
                <logview :parentData="CaseID">
                    
                </logview>
                
            </v-col>

        </v-row>
    </div>
</template>

<script>
import LogViewVue from './components/LogView.vue';
    export default {
        components: {
            'logview':LogViewVue,
        },
        props: {
            propCaseId: {
                required: true
            }
        },
        data() {

            const self = this;

            return {
                LoadingCase: true,
                CaseID: self.propCaseId,
                CaseTitle: null,
                CaseDesc: null,
                CaseDetailitem:null,
                CaseDetail:[{

                }],
                CaseDetails:[{
                    name:'',
                    val:''
                }],
            }
        },
        mounted() {
            const self = this;
            self.loadCase(()=>{});
            self.$store.commit('setBreadcrumbs',[
                {label:'Case Recording',name:''}
            ]);
        },
        methods: {
            loadCase(){
                const self = this;
                var items = '';
                axios.get('/api/admin/caserecording/'+self.CaseID+'/show')
                    .then(function(response) {
                        items = response.data.data;
                        self.CaseTitle = items[0].judul;
                        self.CaseDesc = items[0].isi_laporan;
                        self.CaseDetail = items[0];
                        self.CaseDetails =
                            [
                                {
                                    name:'MSISDN Menghubungi',
                                    val: self.CaseDetail.msisdn_menghubungi
                                },
                                {
                                    name:'MSISDN Bermasalah',
                                    val: self.CaseDetail.msisdn_bermasalah
                                },
                                {
                                    name:'Tanggal Kejadian',
                                    val: self.CaseDetail.tgl_kejadian
                                },
                                {
                                    name:'Agent',
                                    val: self.CaseDetail.id_co
                                },
                                {
                                    name:'Site / Direction | Status | Priority',
                                    val: self.CaseDetail.tipe_layanan + " | " + self.CaseDetail.ket + " | " + self.CaseDetail.priority
                                }
                            ];
                        /*let arr = [
                            ['MSISDN Menghubungi',self.CaseDetail.msisdn_menghubungi],
                            ['MSISDN Bermasalah', self.CaseDetail.msisdn_bermasalah],
                        ];*/




                        self.totalItems = response.data.recordsFiltered;
                        self.pagination.totalItems = self.totalItems;
                        (cb || Function)();

                    }).catch(function(error) {
                    if (error.response && error.response.status != 200) {
                        self.$store.commit('showSnackbar', {
                            message: error.response.status+':'+ error.response.statusText + ' Please Reload Your Browser',
                            color: 'error',
                            duration: 10000
                        });
                    };

                    self.LoadingCase = false;
                })
            }
        }
    }
</script>