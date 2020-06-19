<template>
	<div>
		<ActionBarComponent
				:showBack="true"
				v-on:back="goBack"
				title="Create a new section"
				class="px-24">
		</ActionBarComponent>
		<LocationableInput v-on:selectlocationable="(e) => selectedLocationable = e"></LocationableInput>
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
			<a @click.prevent="goBack" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 bg-box text-primary rounded shadow focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</a>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'
	import LocationableInput from 'Vue/components/locationables/LocationableInput'
	import NProgress from 'nprogress'
	import Swal from 'sweetalert2'

	export default {
		name: 'create-section',

		components: {
			RichTextInput,
			FormInput,
			LocationableInput,
		},

		data() {
			return {
				start_time: "",
				end_time: "",
				content: "",
				draft: true,

				submitText: "Store this report!",
				duration: "--",
				selectedLocationable: null,

				ready: true,
				error: "",
			};
		},

		methods: {
			prt: function(e) {
				console.log(e);
			},
			goBack: function() {
				if (this.start_time !== "" || this.end_time !== "" || this.content !== "" ||
					(this.selectedLocationable !== null && Object.keys(this.selectedLocationable).length !== 0)) {
					Swal.fire({
						title: "Are you sure?",
						text: "When you go back, you'll lose your new post!",
						icon: "warning",
						showCancelButton: true,
						confirmButtonText: 'Yes, go back without saving!',
						customClass: {
							confirmButton: "green-button",
							cancelButton: "red-button",
						},
						target: document.getElementById('parent-element'),
					})
					.then((result) => {
						if (result.value) {
							this.$emit('back');
						}
					});
				} else {
					this.$emit('back');
				}
			},
			submitSection: function() {
				NProgress.start();

				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;

				axios.post(`/api/v1/trips/${tripId}/reports/${reportId}/sections`, {
					start_time: this.start_time,
					end_time: this.end_time,
					content: this.content,
					is_draft: this.draft,
					locationable: this.selectedLocationable,
					visibility: "friends", // TODO
					//published_at for postponed publication
				})
					.then((response) => {
						Swal.fire({
							title: "Done!",
							text: "This section has been created, nice work!",
							icon: "success",
							target: document.getElementById('parent-element'),
						});

						this.$emit('created', response.data);
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
					// log out
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
