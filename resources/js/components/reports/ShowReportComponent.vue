<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
		<router-link :to="{name: 'showTrip', params: {tripId: $route.params.tripId}}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" title="Close this report"><font-awesome-icon :icon="['fas', 'times']" /></router-link>
		<router-link :to="{name: 'editReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-12 mt-8 py-3 right-0 text-2xl text-gray-400 top-0" title="Edit this report"><font-awesome-icon :icon="['fas', 'edit']" /></router-link>
		<div>
			<h1 class="text-6xl tracking-wide uppercase">{{ report.title }}</h1> <!-- TITLE -->
			<span class="block ml-2 mt-1 text-gray-700 text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span> <!-- OWNER -->
			<span class="block ml-2 mt-4 text-2xl">{{ report.date }}</span> <!-- DATE -->
			<p class="leading-normal ml-2 mt-2 text-justify text-md">{{ report.description }}</p> <!-- DESCRIPTION -->
		</div>
		<div class="mt-8 flex flex-row justify-between">
			<div class="flex-grow mr-4 w-2/3"> <!-- SECTIONS -->
				<span class="block text-2xl">Report</span>
				<div class="-ml-20 h-0 rotate-270 sticky text-6xl text-right top-0 uppercase">
					<span class="pr-8">{{ report.title }}</span>
				</div>
				<router-link :to="{name: 'createSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white">Add a section</router-link>
				<div class="bg-gray-100 mt-2 rounded-lg" v-if="sections.length > 0">
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative" @mouseover="mouseOverSection(index)" :key="section.id" v-for="(section, index) in sections">
						<span class="text-gray-500 text-sm uppercase" :title="section.published_at">{{ section.published_at_diff }}</span>
						<span class="block mt-1 text-gray-500 text-xs uppercase">by <router-link :to="{name: 'showProfile', params: {username: section.owner.username}}" class="cursor-pointer hover:underline text-blue-600">{{ section.owner.first_name }} {{ section.owner.middle_name }} {{ section.owner.last_name }}</router-link></span>
						<router-link :to="{name: 'showSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: section.id}}" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer">details</router-link>
						<p class="leading-snug mt-4 text-justify">{{ section.content }}</p>
                        <p class="leading-snug mt-4 text-justify">spotify:track:6xTtThCvuvIYHl1ddntJGj to:</p>
                        <iframe class="bg-white mt-4" style="border-radius: 10px;" src="https://open.spotify.com/embed/track/6xTtThCvuvIYHl1ddntJGj" width="100%" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>
                        <p class="leading-snug mt-4 text-justify">{{ section.content }}</p>
					</div>
				</div>
				<span class="block mt-2 text-gray-700" v-else-if="loading">Loading sections.</span>
				<span class="block mt-2 text-gray-700" v-else>No sections written yet.</span>
			</div>
			<div class="flex-grow w-1/3">
				<div class="sticky top-0 pt-8">
					<div class="bg-gray-100 rounded-lg shadow-lg overflow-hidden">
						<div v-if="sections.length > 0">
							<div class="px-6 py-4 relative">
								<span class="text-4xl">{{ sections[sectionInfoIndex].time }}</span>
								<span class="absolute top-0 right-0 mt-4 mr-5 text-4xl">{{ sections[sectionInfoIndex].duration_formatted }}</span>
								<span class="block text-xl mt-2 text-gray-600 cursor-pointer hover:underline" v-if="sections[sectionInfoIndex].locationable != null">{{ sections[sectionInfoIndex].locationable.name }}</span>
							</div>
							<div class="relative" v-if="sections[sectionInfoIndex].locationable != null">
								<img src="/img/example-map.png" alt="#">
								<div class="absolute bg-gray-100 flex flex-col items-center mr-2 mt-2 px-1 px-2 py-3 right-0 rounded-full text-2xl text-xl top-0">
									<font-awesome-icon class="cursor-default" :icon="['fas', 'map-marker-alt']" />
									<font-awesome-icon class="mt-2 cursor-pointer text-gray-600 hover:text-black" :icon="['far', 'image']" />
								</div>
							</div>
						</div>
						<span class="block mt-2 text-gray-700 px-6 py-4" v-else-if="loading">Loading sections.</span>
						<span class="block mt-2 text-gray-700 px-6 py-4" v-else>When adding sections, location data will be shown here</span>
					</div>
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
                report: {},
                sections: [],
                sectionInfoIndex: 0,
            };
        },
        created() {
        	this.getReport();
        	this.getSections();
        },
        methods: {
            getReport: function() {
            	let tripId = this.$route.params.tripId;
            	let reportId = this.$route.params.reportId;

            	if (this.$store.getters.hasReportWithId(reportId)) {
            		this.report = this.$store.getters.getReportById(reportId)[0];
            		return;
            	}

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}`)
                    .then((response) => {
                    	this.$store.commit('addReport', response.data);
                        this.report = response.data.data;
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
            getSections: function() {
            	axios.get(`/api/v1/trips/${this.$route.params.tripId}/reports/${this.$route.params.reportId}/sections`)
                    .then((response) => {
                    	console.log(response);
                    	this.loading = false;
                        this.sections = response.data.data;
                        this.$store.commit('setSections', response.data.data);
                    })
                    .catch((error) => {
                    	console.log(error);
                        if (error.response.status == 500 || error.response.status == 403) {
                            this.userData = error.response.data;
                        }
                        if (error.response.status == 401) {
                            document.getElementById('logout').submit();
                        }
                        console.log("error: " + error);
                    });
            },
            mouseOverSection: function(index) {
            	if (this.sectionInfoIndex != index) {
            		this.sectionInfoIndex = index;
            	}
            }
        },
    }
</script>