import Vue from 'vue'
import routes from 'Vue/routes'
import { store } from 'Vue/store'
import VueRouter from 'vue-router'
import NProgress from 'nprogress'
import App from 'Vue/App.vue'
import TimeHelperMixin from 'Vue/mixins/TimeHelperMixin'
import AlertHelperMixin from 'Vue/mixins/AlertHelperMixin'
import ProgressHelperMixin from 'Vue/mixins/ProgressHelperMixin'
import ErrorHelperMixin from 'Vue/mixins/ErrorHelperMixin'

require('./bootstrap')
require('./font-awesome')

Vue.component('MapOutputComponent', require('./components/global/MapOutputComponent.vue').default);
Vue.component('MapInputComponent', require('./components/global/MapInputComponent.vue').default);
Vue.component('ErrorHandlerComponent', require('./components/global/ErrorHandlerComponent.vue').default);
Vue.component('ProgressBarComponent', require('./components/global/ProgressBarComponent.vue').default);
Vue.component('ActionBarComponent', require('./components/global/ActionBarComponent.vue').default);
Vue.component('Logo', require('./components/global/Logo.vue').default);

Vue.use(VueRouter);
Vue.mixin(TimeHelperMixin);
Vue.mixin(AlertHelperMixin);
Vue.mixin(ProgressHelperMixin);
Vue.mixin(ErrorHelperMixin);

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
		let verified = store.getters['auth/verified'];

		if (isAuthenticated && to.meta.requiresVerification === true && !verified) {
			next({name: 'verify-email'});
		} else {
			next();
		}
	}
})

store.dispatch('auth/getUser').then(() => {
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
