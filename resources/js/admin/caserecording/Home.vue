<template>
    <div class="page_wrap_vue pa-3">
        <!--<v-fab-transition>
            <v-btn
                    color="primary"
                    fab
                    dark
                    small
                    fixed
                    bottom
                    right
                    @click="showDialog('newcase_dialog')"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </v-fab-transition>-->
        <!-- add newcase -->
        <v-dialog v-model="dialogs.add.show" hide-overlay persistent fullscreen scrollable transition="dialog-bottom-transition">
            <v-card>
                <v-toolbar dark color="primary">
                    <v-btn icon dark @click.native="dialogs.add.show = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>New Case / Case Baru</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn @click="savenewcase()" dark text >Save</v-btn></v-toolbar-items>
                </v-toolbar>
                <NewCaseDialog ref="NewCase"></NewCaseDialog>
            </v-card>
        </v-dialog>
        <file-lists></file-lists>
    </div>

</template>

<style>
    .theme--light.v-tabs-items {
        background-color: transparent;
    }
</style>

<script>
    import NewCaseDialog from './components/NewCaseDialog.vue';
    import FileSearch from './components/FileSearchForm.vue';
    import FileUpload from './components/FileUpload.vue';
    import FileLists from './components/FileLists.vue';
    import FileGroupLists from './components/FileGroupLists.vue';
    export default {
        components: {
            FileSearch,
            NewCaseDialog,
            FileUpload,
            FileGroupLists,
            FileLists
        },
        mounted() {

            const self = this;
            self.$store.commit('setBreadcrumbs',[
                {label:'Case Recording',name:''}
            ]);
        },
        computed: {
            dateRangeText () {
                return this.dates.join(' ~ ')
            },
        },
        data() {
            return {

                isLoading: false,
                dates: [],
                active: 'caserecording',
                newcasedialog: false,
                newcase_dialog:false,
                dialogs: {
                    add: {
                        show: false
                    }
                }
            }
        },
        watch: {
            active(v) {
                console.log('active tab: ' + v);
            }
        },
        methods: {
            process: function(){
                // items is defined object inside data()
                let valid = this.$refs.NewCaseDialog.data('valid')
            },
            savenewcase () {
                this.$refs.NewCase.save()
            },
            showDialog(dialog, data) {
                const self = this;
                switch (dialog){
                    case 'newcase_dialog':
                        setTimeout(()=>{
                            self.dialogs.add.show = true;
                        },500);
                        break;
                }
            },
        }
    }
</script>