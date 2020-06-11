<template>
	<div class="m-auto pl-8 pr-24 w-full">
		<ActionBarComponent
				:backLink="{name: 'showReport', params: {tripId: this.$route.params.tripId, reportId: this.$route.params.reportId}}"
				title="Update report">
		</ActionBarComponent>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3 relative">
				<h1 class="text-4xl tracking-wide">Update report</h1>

				<div class="w-full mt-4">
					<div>
						<label class="text-gray-700 mt-3 block" for="name">Title</label>
						<input v-model="report.title" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="text">
						<div>
							<span></span>
							<span></span>
						</div>
					</div>
					<div>
						<label class="text-gray-700 mt-3 block" for="date">Date</label>
						<input v-model="report.date" name="date" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="date">
						<div>
							<span></span>
							<span></span>
						</div>
					</div>
					<MarkdownInputComponent label="Description" :content.sync="report.description"></MarkdownInputComponent>
					<input @click.prevent="updateReport" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Update this report!">
					<router-link :to="{name: 'showReport', params: {tripId: this.$route.params.tripId, reportId: this.$route.params.reportId}}" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 bg-gray-100 rounded shadow focus:outline-none hover:bg-gray-200 focus:bg-gray-200 focus:shadow-md">Cancel</router-link>
				</div>
			</div>
			<div class="mt-12 w-1/3">
				<div class="px-6 py-4 rounded-lg shadow-md bg-gray-100">
					<span class="block text-xl">Plan</span>
					<ul class="text-gray-700 text-sm">
						<li class="mt-2">No plan yet</li>
						<li class="mt-1 text-blue-600"><a class="hover:underline" href="#">Add a plan</a></li>
					</ul>
				</div>
			</div>
		</div>
		<ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	export default {
		mounted() {

		},
		data() {
			return {
				title: "",
				date: "",
				description: "",
				report: {},

				loading: false,
				error: "",
			};
		},
		created() {
			this.getReport();
		},
		methods: {
			getReport: function() {
				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;

				// if (this.$store.getters.hasReportWithId(reportId)) {
				// 	this.report = _.cloneDeep(this.$store.getters.getReportById(reportId)[0]);
				// 	return;
				// }

				axios.get(`/api/v1/trips/${tripId}/reports/${reportId}`)
					.then((response) => {
						// this.$store.commit('addReport', response.data);
						this.report = response.data.data;
					})
					.catch(this.handleError)
					.finally(this.stopLoading);
			},
			updateReport: function() {
				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;

				axios.put(`/api/v1/trips/${tripId}/reports/${reportId}`, {
					title: this.report.title,
					date: this.report.date,
					description: this.report.description,
					visibility: "friends", // TODO
					//published_at for postponed publication
				})
					.then((response) => {
						this.$router.push(`/app/trips/${tripId}/reports/${reportId}`);
					})
					.catch(this.handleError);
			},
			handleError: function(error) {
				if (error.response.status == 401) {
					document.getElementById('logout').submit();
				}

				this.userData = error.response.data;
			},
			stopLoading: function() {
				this.loading = false;
			}
		},
	}
</script>
