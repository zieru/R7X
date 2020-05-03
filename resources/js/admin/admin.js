/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import colors from "vuetify/lib/util/colors";


// vendor
require('../bootstrap');

window.Vue = require('vue');

// 3rd party
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css';
import Vue from 'vue';
import Vuetify from 'vuetify';
import VueProgressBar from 'vue-progressbar'
import CKEditor from 'ckeditor4-vue';
import JQuery from 'jquery'

window.$ = JQuery



// this is the vuetify theming options
// you can change colors here based on your needs
// and please dont forget to recompile scripts
Vue.use(Vuetify);
Vue.use( CKEditor );
// this is the progress bar settings, you
// can change colors here to fit on your needs or match
// your theming above
Vue.use(VueProgressBar,{
    color: colors.red.darken4,
    failedColor: colors.red.base,
    thickness: '5px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    inverse: false
});

// global component registrations here
Vue.component('fade-loader', require('vue-spinner/src/MoonLoader.vue'));

// app
import router from './router';
import store from '../common/Store';
import eventBus from '../common/Event';
import formatters from '../common/Formatters';
import AxiosAjaxDetct from '../common/AxiosAjaxDetect';
import './sharedcomponent';
// As a plugin
/*import VueMask from 'v-mask'
Vue.use(VueMask);
import VueTheMask from 'vue-the-mask';
Vue.use(VueTheMask);*/
Vue.use(formatters);
Vue.use(eventBus);
/*
const themeCache = new LRU({
    max: 10,
    maxAge: 1000 * 60 * 60, // 1 hour
})
*/

const admin = new Vue({
    vuetify: new Vuetify({
        theme: {
            themes: {
                light: {
                    primary: colors.red.darken4,
                    info: '#4c86b5',
                    success: '#17b535',
                    secondary: colors.grey.lighten4,
                    accent: colors.red.darken2,
                    error: '#b71c1c',
                }
            },
        },
        icons: {
            iconfont: 'mdi'
        },
        options: {
            customProperties: true,

            /*themeCache,*/
            minifyTheme: function (css) {
                return process.env.NODE_ENV === 'production'
                    ? css.replace(/[\r\n|\r|\n]/g, '')
                    : css
            },
        },
    }),
    el: '#admin',
    eventBus,
    router,
    store,
    data: () => ({
        drawer: true,
    }),
    mounted() {

        const self = this;

        // progress bar top
        AxiosAjaxDetct.init(()=>{
            self.$Progress.start();
        },()=>{
            self.$Progress.finish();
        });
    },
    computed: {
        getBreadcrumbs() {
            return store.getters.getBreadcrumbs
        },
        showLoader() {
            return store.getters.showLoader;
        },
        showSnackbar: {
            get() {
                return store.getters.showSnackbar;
            },
            set(val) {
                if(!val) store.commit('hideSnackbar');
            }
        },
        snackbarMessage() {
            return store.getters.snackbarMessage;
        },
        snackbarColor() {
            return store.getters.snackbarColor;
        },
        snackbarDuration() {
            return store.getters.snackbarDuration;
        },

        // dialog
        showDialog: {
            get() {
                return store.getters.showDialog;
            },
            set(val) {
                if(!val) store.commit('hideDialog');
            }
        },
        dialogType() {
            return store.getters.dialogType
        },
        dialogTitle() {
            return store.getters.dialogTitle
        },
        dialogMessage() {
            return store.getters.dialogMessage
        },
        dialogIcon() {
            return store.getters.dialogIcon
        },
    },
    methods: {
        menuClick(routeName, routeType) {

            let rn = routeType || 'vue';

            if(rn==='vue') {

                this.$router.push({name: routeName});
            }
            if(rn==='full_load') {

                window.location.href = routeName;
            }
        },
        clickLogout(logoutUrl,afterLogoutRedirectUrl) {
            axios.post(logoutUrl).then(function(response) {
                    window.location.href = afterLogoutRedirectUrl;
            }).catch(function(error) {
                    alert(error.response.status+':'+ error.response.statusText + ' Please Reload Your Browser');
            });
            /*axios.post(logoutUrl).then((r)=>{
                window.location.href = afterLogoutRedirectUrl;
            })*/
            ;
        },
        dialogOk() {
            store.commit('dialogOk');
        },
        dialogCancel() {
            store.commit('dialogCancel');
        }
    }
});