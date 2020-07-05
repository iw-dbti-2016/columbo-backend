<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:showBack="true"
				v-on:back="$router.push({name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}})"
				title="Update report"
				:showToggleTheme="true"
				class="mt-4">
		</ActionBarComponent>
		<div class="flex flex-row justify-between mt-4">
			<div class="flex-grow pr-8 w-2/3 relative">
				<div class="w-full mt-4">
					<FormInput label="Title" v-model="report.title"></FormInput>
					<FormInput label="Date" type="date" v-model="report.date"></FormInput>
					<RichTextInput label="Description" :content.sync="report.description"></RichTextInput>
					<input @click.prevent="updateReport" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Update this report!">
					<router-link :to="{name: 'showReport', params: {tripId: this.$route.params.tripId, reportId: this.$route.params.reportId}}" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 bg-box rounded shadow focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</router-link>
				</div>
			</div>
			<div class="mt-12 w-1/3">
				<div class="px-6 py-4 rounded-lg shadow-md bg-box">
					<span class="block text-xl text-primary">Plan</span>
					<ul class="text-fade-more text-sm">
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
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'

	export default {
		name: 'edit-report',

		components: {
			RichTextInput,
			FormInput,
		},

		data() {
			return {
				title: "",
				date: "",
				description: "",
				report: {},

				ready: false,
				error: "",
			};
		},

		beforeRouteEnter(to, from, next) {
            next(component => {
				let tripId = component.$route.params.tripId;
				let reportId = component.$route.params.reportId;

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}`)
                    .then(response => {
                    	component.report = response.data;
                    	component.ready = true;

                        component.stopLoading();
                    })
                    .catch(component.handleError)
            })
        },

		methods: {
			updateReport: function() {
				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;

				axios.patch(`/api/v1/trips/${tripId}/reports/${reportId}`, {
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
		},
	}
</script>
