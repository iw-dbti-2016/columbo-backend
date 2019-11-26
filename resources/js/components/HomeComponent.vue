<template>
    <div class="">
        <div class="m-auto max-w-4xl my-8 py-10 w-full relative">
            <h1 class="text-black tracking-wide text-6xl uppercase mb-6">My Trips</h1>
            <router-link :to="{name: 'createTrip'}" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white"><font-awesome-icon :icon="['fas', 'plus']" /> Create new trip</router-link>
            <div class="mb-8 bg-white rounded-lg shadow-md">
                <div class="my-4" v-if="trips.length != 0">
                    <router-link :to="{name: 'showTrip', params: {tripId: trip.id}}" class="block px-8 py-6 border-b border-gray-400 last:border-b-0 cursor-pointer" :key="index" v-for="(trip, index) in trips">{{ trip.name }}</router-link>
                </div>
                <div class="my-4 px-8 py-6" v-else>No trips found...</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getTrips();
        },
        data() {
            return {
                userData: {},
                trips: [],
            };
        },
        methods: {
            getTrips: function() {
                axios.get('/api/v1/user/trips')
                    .then((response) => {
                        this.trips = response.data.data;
                        this.$store.commit('setTrips', response.data.data);
                    })
                    .catch((error) => {
                        if (error.response.status == 500 || error.response.status == 403) {
                            this.userData = error.response.data;
                        }
                        if (error.response.status == 401) {
                            document.getElementById('logout').submit();
                        }
                        console.log("error: " + error);
                    });
            },
            getUserData: function() {
                axios.get('/api/v1/user')
                    .then((response) => {
                        this.userData = response.data.data;
                    })
                    .catch((error) => {
                        if (error.response.status == 500 || error.response.status == 403) {
                            this.userData = error.response.data;
                        }
                        if (error.response.status == 401) {
                            document.getElementById('logout').submit();
                        }
                        console.log("error: " + error);
                    });
            },
            refreshToken: function() {
                axios.patch('/api/v1/auth/refresh')
                    .then((response) => {
                        this.userData = response.data.data;
                    })
                    .catch((error) => {
                        if (error.response.status == 500 || error.response.status == 403) {
                            this.userData = error.response.data;
                        }
                        if (error.response.status == 401) {
                            document.getElementById('logout').submit();
                        }
                        console.log("error: " + error);
                    });
            },
        },
    }
</script>
