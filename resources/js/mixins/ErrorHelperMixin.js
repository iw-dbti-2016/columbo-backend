import { mapActions } from 'vuex'

export default {
	name: 'error-helper-mixin',

	data() {
		return {
			defaultConfig: {
				// Unauthenticated
				401: () => {
					this.logout();
					this.$router.push({name: 'login', query: {force: 'true'}});
				},
				// Unauthorized
				403: () => {
					this.errorAlert({
						title: "You are not authorized",
						text: "The action you were trying to perform is not",
					});
				},
				// Not found
				404: () => {
					this.errorAlert({
						title: "Resource not found",
						text: "We didn't find what we were looking for, try again later!",
					});
				},
				// CSRF Token Mismatch
				419: () => window.location.reload(false),
				// Unprocessable entity: validation error
				422: () => {
					this.warningAlert({
						title: "Validation problem",
						text: "Some of the values you entered are not in the right format",
					});
				},
				// Internal server error
				500: () => {
					this.errorAlert({
						title: "Internal server error",
						text: "We're sorry, that's our mistake... Try again in a bit!"
					});
				},

				// Default
				0: (status) => {
					this.errorAlert({
						title: "Something went wrong",
						text: "Try again later! Errorcode: " + status,
					});
				},
			},
		};
	},

	methods: {
		...mapActions({
			logout: 'auth/logout'
		}),
		handleError(error, config) {
			if (typeof config === 'undefined') {
				config = {};
			}

			let customConfig = {
				...this.defaultConfig,
				...config
			};

			let status = error.response.status;

			if (customConfig.hasOwnProperty(status)) {
				customConfig[error.response.status]();
			} else {
				customConfig[0](status);
			}

			this.stopLoading();
		},
	}
}
