<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
		<router-link :to="{name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" title="Close this section"><font-awesome-icon :icon="['fas', 'times']" /></router-link>
		<router-link :to="{name: 'editSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: $route.params.sectionId}}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-12 mt-8 py-3 right-0 text-2xl text-gray-400 top-0" title="Edit this section"><font-awesome-icon :icon="['fas', 'edit']" /></router-link>
		<div class="flex flex-row justify-between">
			<div class="flex-grow mr-8 w-2/3">
				<span class="block ml-2 mt-1 text-gray-700 text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span>
				<span class="block ml-2 mt-4 text-2xl">{{ section.published_at_diff }}</span>
				<p class="leading-normal ml-2 mt-2 text-justify text-md">{{ section.content }}</p> <!-- DESCRIPTION -->
			</div>
			<div class="flex-grow w-1/3">
				<div class="mt-16 bg-gray-100 rounded-lg shadow-lg overflow-hidden">
					<div class="px-6 py-4 relative">
						<span class="text-4xl">{{ section.time }}</span>
						<span class="absolute top-0 right-0 mt-4 mr-5 text-4xl">{{ section.duration_formatted }}</span>
					</div>
					<div class="relative" v-if="section.locationable != null">
						<img src="/img/example-map.png" alt="#">
						<div class="absolute bg-gray-100 flex flex-col items-center mr-2 mt-2 px-1 px-2 py-3 right-0 rounded-full text-2xl text-xl top-0">
							<font-awesome-icon class="cursor-default" :icon="['fas', 'map-marker-alt']" />
							<font-awesome-icon class="mt-2 cursor-pointer text-gray-600 hover:text-black" :icon="['far', 'image']" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mt-8" v-if="section.locationable != null">
			<span class="block text-2xl">{{ section.locationable.name }}</span>
			<!-- <a @click.prevent="$router.push('/app/reports/create')" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white" href="/app/reports/create">Create a new report</a> -->
			<!-- <span class="block mt-2 text-gray-700">No reports written yet.</span> -->
			<div class="bg-gray-100 mt-2 rounded-lg flex flex-row overflow-hidden shadow-md">
				<div class="flex-grow w-2/3 px-5 py-4 relative">{{ section.locationable.info }}</div>
				<div class="flex flex-col flex-grow items-center justify-around w-1/3">
					<img class="bg-white flex-grow-0 p-2 rounded" src="/img/example-map.png" alt="#">
				</div>
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
                section: {},
            };
        },
        created() {
        	this.getSection();
        },
        methods: {
            getSection: function() {
            	let tripId = this.$route.params.tripId;
            	let reportId = this.$route.params.reportId;
            	let sectionId = this.$route.params.sectionId;

            	if (this.$store.getters.hasSectionWithId(sectionId)) {
            		this.section = this.$store.getters.getSectionById(sectionId)[0];
            		this.loading = false;
            		return;
            	}

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${sectionId}`)
                    .then((response) => {
                    	this.$store.commit('addSection', response.data);
                        this.section = response.data.data;
                        this.loading = false;
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
