<template>
    <div class="">
        <div class="w-full max-w-4xl m-auto my-8">
            <h1 class="text-gray-600 font-light tracking-wider text-5xl mb-6">My Trips</h1>
            <a @click.prevent="createNewTrip" href="#"><font-awesome-icon :icon="['fab', 'font-awesome']" /> Create new trip</a>
            <div class="mb-8 bg-white rounded-lg shadow-md">
                <div class="my-4" v-if="trips.length != 0">
                    <div @click="redirect" class="block px-8 py-6 border-b border-gray-400 last:border-b-0 cursor-pointer" v-for="trip in trips">{{ trip.name }}</div>
                </div>
                <div class="my-4 px-8 py-6" v-else>No trips found...</div>
            </div>

            <div class="flex flex-col justify-around h-full">
                <a @click.prevent="getUserData" href="#">Get your data</a>
                <a @click.prevent="refreshToken" href="#">Refresh token</a>
                <div v-show="userData !== {}">
                    <ul>
                        <li v-for="(item,key) in userData">{{ key }}: {{ item }}</li>
                    </ul>
                </div>
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
                        console.log(response);
                        this.trips = response.data.data;
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
            redirect: function() {
                this.$router.push({path: "/app/trips/1"})
            },
            createNewTrip: function() {
                this.$router.push({path: "/app/trips/create"});
            }
        },
    }
</script>
