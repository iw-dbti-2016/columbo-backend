/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./font-awesome');

import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';

window.Vue = require('vue');

Vue.use(Vuex);
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
const routes = [
	{
		path: '/app/',
		component: require('./components/HomeComponent.vue').default,
	},
	{
		path: '/app/trips/create',
		component: require('./components/trips/CreateTripComponent.vue').default,
	},
	{
		path: '/app/trips/:id/edit',
		component: require('./components/trips/EditTripComponent.vue').default,
	},
	{
		path: '/app/trips/:id',
		component: require('./components/trips/ShowTripComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/create',
		component: require('./components/reports/CreateReportComponent.vue').default,
	},
	{
		path: '/app/reports/:id/edit',
		component: require('./components/reports/EditReportComponent.vue').default,
	},
	{
		path: '/app/reports/:id',
		component: require('./components/reports/ShowReportComponent.vue').default,
	},
	{
		path: '/app/reports/:reportId/sections/create',
		component: require('./components/sections/CreateSectionComponent.vue').default,
	},
	{
		path: '/app/sections/:id/edit',
		component: require('./components/sections/EditSectionComponent.vue').default,
	},
	{
		path: '/app/sections/:id',
		component: require('./components/sections/ShowSectionComponent.vue').default,
	}
];

const router = new VueRouter({
	mode: 'history',
	routes: routes,
});

const store = new Vuex.Store({
	state: {
		user: [],
		users: [],
		trips: [],
		reports: [],
		sections: [],
	},
	mutations: {
		setTrips (state, payload) {
			state.trips = payload;
		},
		addTrip: (state, payload) => {
			state.trips.push(payload.data);
		},
	},
	getters: {
		getTripById: (state) => (id) => {
			return state.trips.filter(trip => trip.id == id);
		},
		hasTripWithId: (state, getters) => (id) => {
			return getters.getTripById(id).length;
		},
		getTrips: (state) => {
			return state.trips;
		},
		hasTrips: (state, getters) => {
			return Object.keys(getters.getTrips).length !== 0;
		}
	}
});

const app = new Vue({
	router: router,
	store,
	el: "#app",
});
