<template>
    <div class="page_wrap_vue">
        <v-tabs color="primary" v-model="active">

            <v-tab  key="files" href="#files" ripple>
                Files
            </v-tab>
            <v-tab v-if="managefilegroup != false" key="manage-groups" href="#manage-groups" ripple>
                Manage File Groups
            </v-tab>
            <v-tab key="upload" href="#upload" ripple>
                Upload
            </v-tab>

            <v-tab-item value="files">
                <v-card flat>
                    <v-card-text>
                        <file-lists></file-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item v-if="managefilegroup != false" value="manage-groups">
                <v-card flat>
                    <v-card-text>
                        <file-group-lists></file-group-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item value="upload">
                <v-card flat>
                    <v-card-text>
                        <file-upload></file-upload>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
    import FileGroupLists from './components/FileGroupLists.vue';
    import FileUpload from './components/FileUpload.vue';
    import FileLists from './components/FileLists.vue';
    export default {
        components: {
            FileUpload,
            FileGroupLists,
            FileLists
        },
        props:{
            managefilegrouptab: {
                type:Boolean,
                default:true
            }
        },
        mounted() {
            console.log('pages.FileManager.vue');

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Files',name:''}
            ]);
        },
        data() {
            return {
                managefilegroup: this.managefilegrouptab,
                active: 'files'
            }
        },
        watch: {
            active(v) {
                console.log(this.managefilegroup);
                console.log('active tab: ' + v);
            }
        },
        methods: {
        }
    }
</script>

<style scoped="">
    .finder_wrap {
        padding: 0 20px;
    }
</style>