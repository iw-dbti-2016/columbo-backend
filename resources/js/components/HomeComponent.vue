<template>
	<div v-if="ready">
		<div class="h-10 flex items-center absolute top-0 right-0 mr-24 mt-6">
			<a class="bg-gray-800 text-white inline-block px-4 py-2 rounded cursor-pointer" @click="signout">Log out</a>
		</div>
		<div class="m-auto pl-8 pr-24 mt-20 flex justify-between">
			<div class="w-3/4">
				<h1 class="text-white tracking-wide text-5xl uppercase">Timeline</h1>
			</div>
			<div class="w-1/4">
				<h1 class="text-white tracking-wide text-5xl uppercase mb-6">Trips</h1>
				<router-link :to="{name: 'createTrip'}" class="bg-green-800 inline-block mt-2 px-4 py-2 rounded text-white cursor-pointer"><font-awesome-icon :icon="['fas', 'plus']" /> Create new trip</router-link>
				<div class="mb-8 bg-gray-600 rounded-md shadow-md">
					<div class="my-4" v-if="trips.length != 0">
						<router-link :to="{name: 'showTrip', params: {tripId: trip.id}}" class="block px-8 py-6 border-b border-gray-400 last:border-b-0 cursor-pointer" :key="index" v-for="(trip, index) in trips">{{ trip.name }}</router-link>
					</div>
					<div class="my-4 px-8 py-6" v-else>No trips found...</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import NProgress from 'nprogress'
	import { mapActions } from 'vuex'

	export default {
		name: 'home',

		data() {
			return {
				user: this.$store.getters['auth/user'],
				ready: false,
				trips: [],
			};
		},

		beforeRouteEnter(to, from, next) {
            next(component => {
                axios.get(`/api/v1/trips`)
                    .then(response => {
                    	component.trips = response.data;
                    	component.ready = true;

                        NProgress.done()
                    })
                    .catch(component.handleError)
            })
        },

		methods: {
			...mapActions({
				logout: 'auth/logout'
			}),
			async signout() {
				await this.logout();

				this.$router.replace({name: 'login'});
			},
			refreshToken: function() {
				axios.patch('/api/v1/auth/refresh')
					.then((response) => {
						this.userData = response.data.data;
					})
					.catch(this.handleError);
			},
			handleError: function(error) {
				if (error.response.status == 500 || error.response.status == 403) {
					this.userData = error.response.data;
				}
				if (error.response.status == 401) {
					// document.getElementById('logout').submit();
				}
				console.log("error: " + error);
			}
		},
	}
</script>
