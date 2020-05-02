import Vue from 'vue';
import UserProfileTitleCard from './components/user/user_profile_title_card.vue';
import Editor from './components/editor/ckeditor';

Vue.component('Editor',
    () => import('./components/editor/ckeditor.vue')
)
Vue.component('UserProfileTitleCard',
    () => import('./components/user/user_profile_title_card.vue')
)