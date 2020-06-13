import Vue from 'vue'
import routes from './routes'
import { store } from './store'
import VueRouter from 'vue-router'
import NProgress from 'nprogress'
import App from './App.vue'

require('./bootstrap')
require('./font-awesome')

Vue.component('MapOutputComponent', require('./components/global/MapOutputComponent.vue').default);
Vue.component('MapInputComponent', require('./components/global/MapInputComponent.vue').default);
Vue.component('ErrorHandlerComponent', require('./components/global/ErrorHandlerComponent.vue').default);
Vue.component('ProgressBarComponent', require('./components/global/ProgressBarComponent.vue').default);
Vue.component('ActionBarComponent', require('./components/global/ActionBarComponent.vue').default);

Vue.use(VueRouter);

const router = new VueRouter({
	routes: routes,
	mode: 'history',
});

router.beforeEach((to, from, next) => {
	NProgress.start()

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

		data: {
			theme: 'dark-mode',
		},

		mounted() {
			this.$root.theme = localStorage.getItem("theme") || 'light-mode';
		},

		methods: {
			toggleTheme() {
				this.$root.theme = (this.theme === 'light-mode') ? 'dark-mode' : 'light-mode';
				localStorage.setItem("theme", this.$root.theme);
			}
		}
	}).$mount("#app")
})
