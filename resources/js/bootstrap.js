import NProgress from 'nprogress'


////////////////////////
// CONFIGURE PACKAGES //
////////////////////////
NProgress.configure({
    showSpinner: false,
    easing: 'ease',
    speed: 250,
})

//////////////////////
// GLOBAL VARIABLES //
//////////////////////
window._ = require('lodash');

window.Vue = require('vue')
window.dompurify = require('dompurify');

var baseUrl = document.head.querySelector('meta[name="base-url"]').content;

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true
window.axios.defaults.baseURL = baseUrl;

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
