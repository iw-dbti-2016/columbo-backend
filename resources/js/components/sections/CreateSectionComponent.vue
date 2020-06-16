<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:backLink="{name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}"
				backTitle="Discard and go back"
				title="Create a new section">
		</ActionBarComponent>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3 relative">
				<div class="w-full mt-4">
		<div class="mt-4 mx-24">
			<div class="mt-2 w-full flex flex-row justify-between">
				<div class="flex-grow w-1/2 mr-4">
					<FormInput label="Start time" type="time" v-model="start_time"></FormInput>
				</div>
				<div class="flex-grow w-1/2">
					<FormInput label="End time" type="time" v-model="end_time"></FormInput>
				</div>
			</div>
			<span class="text-fade">Duration: {{ duration }}</span>
			<RichTextInput label="Content" :content.sync="content"></RichTextInput>
			<div>
				<label class="text-primary mt-3 block" for="draft">
					<input v-model="draft" name="draft" id="draft" class="text-primary inline-block mt-2 px-4 py-3" type="checkbox">
					<span>This is a draft</span>
				</label>
				<div>
					<span></span>
					<span></span>
				</div>
			</div>
			<input @click.prevent="submitSection" class="inline-block mt-4 px-4 py-3 bg-green-800 rounded text-white cursor-pointer focus:outline-none hover:bg-green-700 focus:bg-green-700 focus:shadow-lg" type="submit" :value="submitText">
			<router-link :to="{name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 bg-box text-primary rounded shadow focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</router-link>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import NProgress from 'nprogress'
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'

	export default {
		name: 'create-section',

		components: {
			RichTextInput,
			FormInput,
		},

		data() {
			return {
				start_time: "",
				end_time: "",
				content: "",
				draft: true,

				submitText: "Store this report!",
				duration: "--",

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
			submitSection: function() {
				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;

				axios.post(`/api/v1/trips/${tripId}/reports/${reportId}/sections`, {
					start_time: this.start_time,
					end_time: this.end_time,
					content: this.content,
					is_draft: this.draft,
					visibility: "friends", // TODO
					//published_at for postponed publication
				})
					.then((response) => {
						this.$router.push({name: 'showSection', params: {tripId: tripId, reportId: reportId, sectionId: response.data.id}});
					})
					.catch(this.handleError);
			},
			calculateDuration: function() {
				let start = this.start_time.split(":");
				let end = this.end_time.split(":");

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
			}
		},
		watch: {
			draft: function(val) {
				this.submitText = val ? 'Store this report!' : 'Create this report!';
			},
			start_time: function(val) {
				let start = val.split(":");
				let end = this.end_time.split(":");

				if (start.length != 2 || end.length != 2) {
					this.duration = "--";
					return;
				}

				this.duration = (end[0] - start[0]) * 60 + (end[1] - start[1]);
			},
			end_time: function(val) {
				let start = this.start_time.split(":");
				let end = val.split(":");

				if (start.length != 2 || end.length != 2) {
					this.duration = "--";
					return;
				}

				this.duration = (end[0] - start[0]) * 60 + (end[1] - start[1]);
			}
		}
	}
</script>
