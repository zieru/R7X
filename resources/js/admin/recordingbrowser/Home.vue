<template>
    <div class="page_wrap_vue pa-3">
        <h2>HOME PAGE</h2>

        <p>I am located at <b>resources/js/admin/dashboard/Home.vue</b></p>
        <p>single page routes for admin can be found at <b>resources/js/admin/router.js</b></p>

        <v-tabs color="primary" v-model="active" vertical>
            <v-tab key="filesearch" href="#filesearch" ripple>
                File Search
            </v-tab>
            <v-tab key="files" href="#files" ripple>
                Files
            </v-tab>
            <v-tab key="manage-groups" href="#manage-groups" ripple>
                Manage File Groups
            </v-tab>
            <v-tab key="upload" href="#upload" ripple>
                Upload
            </v-tab>
            <v-tab-item value="filesearch">
                <v-card flat>
                    <v-card-text>
                        <file-search></file-search>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item value="files">
                <v-card flat>
                    <v-card-text>
                        <file-lists></file-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item value="manage-groups">
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
    import FileSearch from './components/FileSearchForm.vue';
    import FileUpload from './components/FileUpload.vue';
    import FileLists from './components/FileLists.vue';
    export default {
        components: {
            FileSearch,
            FileUpload,
            FileGroupLists,
            FileLists
        },
        mounted() {
            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Recording Browser',name:''}
            ]);
        },
        computed: {
            dateRangeText () {
                return this.dates.join(' ~ ')
            },
        },
        data() {
            return {
                dates: ['2019-09-10', '2019-09-20'],
                active: 'filesearch'
            }
        },
        watch: {
            active(v) {
                console.log('active tab: ' + v);
            }
        },
        methods: {
        }
    }
</script>