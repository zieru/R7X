import Vue from 'vue';
import UserProfileTitleCard from './components/user/user_profile_title_card.vue';
import Editor from './components/editor/ckeditor';
import Files from './files/Files';
import FileLists from './files/components/FileLists';

Vue.component('Editor',
    () => import('./components/editor/ckeditor.vue')
)
Vue.component('Files',
    () => import('./files/Files')
)
Vue.component('FileLists',
    () => import('./files/components/FileLists')
)
Vue.component('UserProfileTitleCard',
    () => import('./components/user/user_profile_title_card.vue')
)