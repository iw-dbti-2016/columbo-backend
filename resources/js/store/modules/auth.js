import axios from 'axios'

export default {
	namespaced: true,

	state: {
		authenticated: false,
		user: null
	},

	getters: {
		authenticated (state) {
			return state.authenticated
		},

		verified (state) {
			return state.user.email_verified_at != null
		},

		user (state) {
			return state.user
		},
	},

	mutations: {
		SET_AUTHENTICATED (state, value) {
			state.authenticated = value
		},

		SET_USER (state, value) {
			state.user = value
		}
	},

	actions: {
		async login({ dispatch }, credentials) {
			await axios.get('/sanctum/csrf-cookie')
			await axios.post('/api/v1/auth/login', {...credentials, "device_name": "browser"})

			return dispatch('getUser')
		},

		async logout({ dispatch }) {
			await axios.delete('/api/v1/auth/logout')

			return dispatch('getUser')
		},

		getUser ({ commit }) {
			return axios.get('/api/v1/user').then((response) => {
				commit('SET_AUTHENTICATED', true)
				commit('SET_USER', response.data)
			}).catch(() => {
				commit('SET_AUTHENTICATED', false)
				commit('SET_USER', null)
			})
		}
	}
}
