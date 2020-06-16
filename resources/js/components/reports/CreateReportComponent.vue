<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:backLink="{name: 'showTrip', params: {tripId: this.$route.params.tripId}}"
				title="Create a new report">
		</ActionBarComponent>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3 relative">
				<div class="w-full mt-4">
					<FormInput label="Title" v-model="title"></FormInput>
					<FormInput label="Date" type="date" v-model="date"></FormInput>
					<RichTextInput label="Description" :content.sync="description"></RichTextInput>
					<input @click.prevent="submitReport" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Create this report!">
					<router-link :to="{name: 'showTrip', params: {tripId: this.$route.params.tripId}}" class="inline-block text-primary absolute right-0 mr-8 mt-4 px-4 py-3 bg-box rounded shadow focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</router-link>
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
	import NProgress from 'nprogress'
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'

	export default {
		name: 'create-report',

		components: {
			RichTextInput,
			FormInput,
		},

		data() {
			return {
				title: "",
				date: "",
				description: "",

				ready: false,
				error: "",
			};
		},

		beforeRouteEnter(to, from, next) {
            next(component => {
            	component.ready = true;

                NProgress.done()
            })
        },

		methods: {
			submitReport: function() {
				let tripId = this.$route.params.tripId;

				axios.post(`/api/v1/trips/${tripId}/reports`, {
					title: this.title,
					date: this.date,
					description: this.description,
					visibility: "friends", // TODO
					//published_at for postponed publication
				})
					.then((response) => {
						this.$router.push(`/app/trips/${tripId}/reports/${response.data.id}`);
					})
					.catch((error) => {
						if (error.response.status == 401) {
							document.getElementById('logout').submit();
						}

						this.userData = error.response.data;
					});
			},
		},
	}
</script>
