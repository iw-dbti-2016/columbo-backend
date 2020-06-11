/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue'
import routes from './routes'
import { store } from './store'
import VueRouter from 'vue-router'
import App from './App.vue'

require('./bootstrap')
require('./font-awesome')

window.Vue = require('vue')

Vue.use(VueRouter);

var baseUrl = document.head.querySelector('meta[name="base-url"]').content;
let csrf = document.head.querySelector('meta[name="csrf-token"]').content;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
	routes: routes,
	mode: 'history',
});

Vue.component('MarkdownOutputComponent', require('./components/global/MarkdownOutputComponent.vue').default);
Vue.component('MarkdownInputComponent', require('./components/global/MarkdownInputComponent.vue').default);
Vue.component('MapOutputComponent', require('./components/global/MapOutputComponent.vue').default);
Vue.component('MapInputComponent', require('./components/global/MapInputComponent.vue').default);
Vue.component('ErrorHandlerComponent', require('./components/global/ErrorHandlerComponent.vue').default);
Vue.component('ProgressBarComponent', require('./components/global/ProgressBarComponent.vue').default);

router.beforeEach((to, from, next) => {
	let isAuthenticated = store.getters['auth/authenticated'];

	if (to.meta.requiresAuthentication === true && !isAuthenticated) {
		next({name: 'login'})
	} else if (to.meta.requiresAuthentication === false && isAuthenticated) {
		next({name: 'home'})
	} else {
		next()
	}
})

store.dispatch('auth/me').then(() => {
	new Vue({
		router,
		store,
		render: h => h(App),
	}).$mount("#app")
})
