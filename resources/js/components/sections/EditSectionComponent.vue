<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
		<router-link :to="{name: 'showSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: $route.params.sectionId}}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" title="Cancel"><font-awesome-icon :icon="['fas', 'times']" /></router-link>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3 relative">
				<h1 class="text-4xl tracking-wide">Update section</h1>

				<div class="w-full mt-4">
					<span class="mt-2 text-lg font-bold align-baseline">06/07/2020 <span class="text-xs font-light">(from report)</span></span>
					<div class="mt-2 w-full flex flex-row justify-between">
						<div class="flex-grow w-1/2 mr-4">
							<label class="text-gray-700 mt-3 block" for="">Start time</label>
							<input v-model="section.start_time" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="time">
							<div>
								<span></span>
								<span></span>
							</div>
						</div>
						<div class="flex-grow w-1/2">
							<label class="text-gray-700 mt-3 block" for="">End time</label>
							<input v-model="section.end_time" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="time">
							<div>
								<span></span>
								<span></span>
							</div>
						</div>
					</div>
					<span class="">Duration: {{ section.duration_minutes }}</span>
					<div>
						<label class="text-gray-700 mt-3 block" for="content">Content</label>
						<textarea v-model="section.content" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" name="" id="" cols="30" rows="10"></textarea>
						<div>
							<span></span>
							<span></span>
						</div>
					</div>
					<div>
						<label class="text-gray-700 mt-3 block" for="draft">
							<input v-model="section.is_draft" name="draft" id="draft" class="inline-block mt-2 px-4 py-3" type="checkbox">
							<span>This is a draft</span>
						</label>
						<div>
							<span></span>
							<span></span>
						</div>
					</div>
					<input @click.prevent="updateSection" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Update this report!">
					<router-link :to="{name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 bg-gray-100 rounded shadow focus:outline-none hover:bg-gray-200 focus:bg-gray-200 focus:shadow-md">Cancel</router-link>
				</div>
			</div>
			<div class="mt-12 w-1/3">
				<div class="px-6 py-4 rounded-lg shadow-md bg-gray-100">
					<span class="block text-xl">Location/POI</span>
					<ul class="text-gray-700 text-sm">
						<li class="mt-2">No location or POI yet</li>
						<li class="mt-1 text-blue-600"><a class="hover:underline" href="#">Add a location</a></li>
						<li class="mt-1 text-blue-600"><a class="hover:underline" href="#">Add a POI</a></li>
						<li class="mt-4 font-bold">You can only add one location or one POI</li>
					</ul>
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
				section: {},
				duration: "--",
			};
		},
		created() {
			this.getSection();
		},
		methods: {
			updateSection: function() {
				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;
				let sectionId = this.$route.params.sectionId;

				axios.put(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${sectionId}`, {
					start_time: this.section.start_time,
					end_time: this.section.end_time,
					content: this.section.content,
					is_draft: this.section.is_draft,
					visibility: "friends", // TODO
					//published_at for postponed publication
				})
					.then((response) => {
						console.log(response);
						this.$router.push({name: 'showSection', params: {tripId: tripId, reportId: reportId, sectionId: response.data.data.id}});
					})
					.catch((error) => {
						console.log("error: " + error);
						console.log(error.response.data);
					});
			},
			calculateDuration: function() {
				let start = this.startTime.split(":");
				let end = this.endTime.split(":");

				if (start.length != 2 || end.length != 2) {
					return 0;
				}

				return (end[0] - start[0]) * 60 + (end[1] - start[1]);
			},
			getSection: function() {
            	let tripId = this.$route.params.tripId;
            	let reportId = this.$route.params.reportId;
            	let sectionId = this.$route.params.sectionId;

            	if (this.$store.getters.hasSectionWithId(sectionId)) {
            		this.section = this.$store.getters.getSectionById(sectionId)[0];
            		return;
            	}

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${sectionId}`)
                    .then((response) => {
                    	this.$store.commit('addSection', response.data);
                        this.section = response.data.data;
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
		watch: {

		}
	}
</script>
