<template>
	<div>
		<ActionBarComponent
				:showBack="true"
				v-on:back="goBack"
				title="Create a new section"
				class="px-24">
		</ActionBarComponent>
		<div class="mt-4 mx-24">
			<div class="mt-2 w-full flex flex-row justify-between">
				<div class="flex-grow w-1/2 mr-4">
					<FormInput label="Start time" type="time" v-model="start_time"></FormInput>
				</div>
				<div class="flex-grow w-1/2">
					<FormInput label="End time" type="time" v-model="end_time"></FormInput>
				</div>
			</div>
			<div class="mt-2 w-full flex flex-row justify-between">
				<div class="flex-grow w-1/2 mr-4">
					<WeatherIconInput @selected="selectWeatherIcon"></WeatherIconInput>
				</div>
				<div class="flex-grow w-1/2">
					<FormInput label="Temperature" type="number" v-model="temperature"></FormInput>
				</div>
			</div>
			<FileInput @selected="(img) => image = img"></FileInput>
			<FormInput class="mt-2" label="Image caption" v-model="image_caption"></FormInput>
			<RichTextInput label="Content" :content.sync="content" :locationables="locationables" @selectlocationable="addLocationable"  @detachlocationable="detachLocationable"></RichTextInput>
			<div @click.prevent="is_draft = !is_draft" class="bg-box cursor-pointer flex items-center mt-10 rounded text-primary">
				<div class="p-3 rounded-l text-2xl text-white bg-green-700" :class="{'text-primary bg-box': !is_draft}">
					<font-awesome-icon :icon="['far', 'square']" v-if="!is_draft"></font-awesome-icon>
					<font-awesome-icon :icon="['far', 'check-square']" v-else></font-awesome-icon>
				</div>
				<div class="ml-3">This is a draft</div>
			</div>
			<div v-if="!is_draft" @click.prevent="delayed_publishing = !delayed_publishing" class="bg-box cursor-pointer flex items-center mt-2 rounded text-primary">
				<div class="p-3 rounded-l text-2xl text-white bg-green-700" :class="{'text-primary bg-box': !delayed_publishing}">
					<font-awesome-icon :icon="['far', 'square']" v-if="!delayed_publishing"></font-awesome-icon>
					<font-awesome-icon :icon="['far', 'check-square']" v-else></font-awesome-icon>
				</div>
				<div class="ml-3">Publish date and time</div>
			</div>
			<FormInput v-if="!is_draft && delayed_publishing" class="mt-2" label="Publish date and time" type="datetime-local" v-model="published_at"></FormInput>
			<input @click.prevent="submitSection" class="inline-block mt-5 px-4 py-3 bg-green-800 rounded text-white cursor-pointer focus:outline-none hover:bg-green-700 focus:bg-green-700 focus:shadow-lg" type="submit" :value="submitText">
			<a @click.prevent="goBack" class="inline-block ml-4 px-4 py-3 bg-box text-primary rounded shadow focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</a>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'
	import FileInput from 'Vue/components/forms/FileInput'
	import WeatherIconInput from 'Vue/components/forms/WeatherIconInput'
	import LocationableInput from 'Vue/components/locationables/LocationableInput'
	import NProgress from 'nprogress'
	import Swal from 'sweetalert2'

	export default {
		name: 'create-section',

		components: {
			RichTextInput,
			FormInput,
			FileInput,
			WeatherIconInput,
			LocationableInput,
		},

		data() {
			return {
				start_time: "",
				end_time: "",
				temperature: null,
				image: null,
				image_caption: null,
				content: "",
				is_draft: true,
				published_at: null,
				weather_icon: 'thermometer-half',

				submitText: "Store this report!",
				duration: "--",
				delayed_publishing: false,
				locationables: [],

				ready: true,
				error: "",
			};
		},

		methods: {
			selectWeatherIcon(icon) {
				this.weather_icon = icon;
			},
			addLocationable(e) {
				if (! this.locationables.some(value => {
					return value.type === e.type && value.id == e.id;
				})) {
					this.locationables.push(e);
				}
			},
			detachLocationable(e) {
				this.locationables = this.locationables.filter(value => {
					return ! (value.type === e.type && value.id == e.id);
				});
			},
			goBack: function() {
				if (this.start_time !== "" || this.end_time !== "" || this.content !== "" ||
					this.locationables.length !== 0) {
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
					weather_icon: this.weather_icon,
					temperature: this.temperature,
					...(this.image !== null) ? {image_file: this.image} : {},
					image_caption: this.image_caption,
					content: this.content,
					is_draft: this.is_draft,
					...(this.locationables !== null) ? {locationables: this.locationables} : {},
					...(this.delayed_publishing) ? {published_at: this.toUTC(this.published_at)} : {},
					visibility: "friends", // TODO
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

				console.log(error.response);

				//this.error = error.response.data;
			}
		},

		watch: {
			is_draft: function(val) {
				this.submitText = val ? 'Save this section!' : 'Publish this section!';
			},
			delayed_publishing: function(val) {
				this.submitText = val ? 'Plan the publishing of this section!' : 'Publish this section!';
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
