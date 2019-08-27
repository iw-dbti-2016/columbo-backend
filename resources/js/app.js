/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router';

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

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('auth-image', require('./components/auth/AuthImage.vue').default);
Vue.component('login-form', require('./components/auth/LoginForm.vue').default);
Vue.component('register-form', require('./components/auth/RegisterForm.vue').default);
// Vue.component('forgot-password-form', require('./components/auth/ForgotPasswordForm.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const routes = [
	{
		path: '/auth/login',
		component: require('./components/auth/LoginForm.vue').default,
		props: {
			action: baseUrl + '/login',
			image: 'travelers.png',
			alt: 'Travelers',
			csrf: csrf,
		},
	},
	{
		path: '/auth/register',
		component: require('./components/auth/RegisterForm.vue').default,
		props: {
			action: baseUrl + '/register',
			image: 'destination.png',
			alt: 'Destination',
			csrf: csrf,
		},
	},
	{
		path: '/auth/forgot-password',
		component: require('./components/auth/ForgotPasswordForm.vue').default,
		props: {
			action: baseUrl + '/password/email',
			csrf: csrf,
		},
	}
]

const router = new VueRouter({
	mode: 'history',
	routes: routes,
});

const app = new Vue({
    router,
    props: ['action', 'source', 'image', 'alt'],
    data: {
    	form: 'login',
    	images: {
    		login: {
    			source: "travelers.png",
    			alt: "Travelers",
    		},
    		register: {
    			source: "destination.png",
    			alt: "Destinations",
    		},
    		forgotPassword: {
    			source: "",
    			alt: "",
    		}
    	}
    },
    methods: {
    	setForm: function(form) {
    		this.form = form;
    	}
    },
    computed: {
    	formImage: function() {
    		return this.images[this.form];
    	},
    }
}).$mount('#app');
