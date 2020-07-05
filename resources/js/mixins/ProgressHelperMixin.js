import NProgress from 'nprogress'

export default {
	name: 'progress-helper-mixin',

	methods: {
		startLoading() {
			NProgress.start();
		},
		stopLoading() {
			NProgress.done();
		},
	}
}
