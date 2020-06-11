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

			return dispatch('me')
		},

		async logout({ dispatch }) {
			await axios.delete('/api/v1/auth/logout')

			return dispatch('me')
		},

		me ({ commit }) {
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
