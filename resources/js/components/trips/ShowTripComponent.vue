<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
		<a @click.prevent="$router.push('/app')" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" href="/app" title="Close this trip"><font-awesome-icon :icon="['fas', 'times']" /></a>
		<a @click.prevent="$router.push('/app/trips/1/edit')" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-12 mt-8 py-3 right-0 text-2xl text-gray-400 top-0" href="/app/trips/1/edit" title="Edit this trip"><font-awesome-icon :icon="['fas', 'edit']" /></a>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3">
				<h1 class="text-6xl tracking-wide uppercase">{{ trip.name }}</h1> <!-- NAME -->
				<p class="ml-2 text-gray-700 text-sm">{{ trip.synopsis }}</p> <!-- SYNOPSIS -->
				<span class="block ml-2 mt-1 text-gray-700 text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span> <!-- OWNER -->
				<span class="block ml-2 mt-3 text-gray-700 text-lg">{{ trip.start_date }} - {{ trip.end_date }}</span> <!-- START AND END DATE -->
				<p class="leading-normal ml-2 mt-3 text-justify text-md">{{ trip.description }}</p> <!-- DESCRIPTION -->
			</div>
			<div class="mt-12 w-1/3">
				<div class="bg-gray-100 px-6 py-4 rounded-lg shadow-md">
					<span class="block text-xl">Members</span> <!-- MEMBERS -->
					<ul class="text-gray-700 text-sm">
						<li class="mt-2"><a class="hover:underline" href="#">Stan Kelchtermans</a></li>
						<li class="mt-1"><a class="hover:underline" href="#">Maikel Both</a></li>
						<li class="mt-1"><a class="hover:underline" href="#">Devin Pelckmans</a></li>
						<li class="mt-1"><a class="hover:underline" href="#">Vik Vanderlinden</a></li>
					</ul>
					<span class="block mt-3 text-xl">Visitors</span> <!-- VISITORS -->
					<span class="block mt-2 text-gray-700 text-sm">No visitors on this trip.</span>
				</div>
			</div>
		</div>
		<div class="mt-8 flex flex-row justify-between">
			<div class="flex-grow mr-4 w-1/2"> <!-- REPORTS -->
				<span class="block text-2xl">Reports</span>
				<a @click.prevent="$router.push('/app/trips/1/reports/create')" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white" href="/app/reports/create">Create a new report</a>
				<span v-if="loading" class="block mt-2 text-gray-700">Loading reports...</span>
				<span v-else-if="reports.length == 0" class="block mt-2 text-gray-700">No reports written yet.</span>
				<div v-else class="bg-gray-100 mt-2 rounded-lg shadow-md">
					<div v-for="report in reports" @click.prevent="$router.push('/app/reports/' + report.id)" class="border-b border-gray-400 last:border-b-0 px-5 py-4 text-md cursor-pointer">{{ report.title }}</div>
				</div>
			</div>
			<div class="flex-grow w-1/2"> <!-- PLANS -->
				<span class="block text-2xl">Planning</span>
				<span class="block mt-2 text-gray-700">No planning determined yet.</span>
				<span class="block mt-2 text-gray-700">Planning is not supported yet!</span>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		mounted() {

        },
        data() {
            return {
            	loading: true,
                trip: {},
                reports: [],
            };
        },
        created() {
        	this.getTrip();
        	this.getReports();
        },
        methods: {
            getTrip: function() {
            	let tripId = this.$route.params.id;

            	if (this.$store.getters.hasTripWithId(tripId)) {
            		this.trip = this.$store.getters.getTripById(tripId)[0];
            		return;
            	}

                axios.get('/api/v1/trips/' + tripId)
                    .then((response) => {
                    	this.$store.commit('addTrip', response.data);
                        this.trip = response.data.data;
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
            getReports: function() {
            	axios.get('/api/v1/trips/' + this.$route.params.id + '/reports')
                    .then((response) => {
                    	this.loading = false;
                        this.reports = response.data.data;
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
            }
        },
    }
</script>
