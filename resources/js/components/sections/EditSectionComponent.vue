<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:backLink="{name: 'showSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: $route.params.sectionId}}"
				title="Update section">
		</ActionBarComponent>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3 relative">
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
					<RichTextInput :content.sync="section.content"></RichTextInput>
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
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import NProgress from 'nprogress'

	export default {
		name: 'edit-section',

		data() {
			return {
				section: {},
				duration: "--",
				preview: false,

				ready: false,
				error: "",
			};
		},

		beforeRouteEnter(to, from, next) {
            next(component => {
            	let tripId = component.$route.params.tripId;
            	let reportId = component.$route.params.reportId;
            	let sectionId = component.$route.params.sectionId;

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${sectionId}`)
                    .then(response => {
                    	component.section = response.data;
                    	component.ready = true;

                        NProgress.done()
                    })
                    .catch(component.handleError)
            })
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
						this.$router.push({name: 'showSection', params: {tripId: tripId, reportId: reportId, sectionId: response.data.data.id}});
					})
					.catch(this.handleError)
			},
			calculateDuration: function() {
				let start = this.startTime.split(":");
				let end = this.endTime.split(":");

				if (start.length != 2 || end.length != 2) {
					return 0;
				}

				return (end[0] - start[0]) * 60 + (end[1] - start[1]);
			},
            handleError: function(error) {
				if (error.response.status == 401) {
					document.getElementById('logout').submit();
				}

				this.error = error.response.data;
            },
		},
	}
</script>
