import Vue from 'vue'
import Vuex from 'vuex'
import { state } from './store/state'
import { actions } from './store/actions'
import { getters } from './store/getters'
import { mutations } from './store/mutations'
import auth from './store/modules/auth'

Vue.use(Vuex)

export const store = new Vuex.Store({
	modules: {
		auth
	},
	state: state,
	mutations: mutations,
	actions: actions,
	getters: getters,
})
