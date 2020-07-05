<template>
	<div>
		<ActionBarComponent
				:showBack="true"
				v-on:back="goBack"
				title="Update section"
				class="px-24">
		</ActionBarComponent>
		<div class="mt-4 px-24">
			<div class="mt-2 w-full flex flex-row justify-between">
				<div class="flex-grow w-1/2 mr-4">
					<FormInput label="Start time" type="time" v-model="section.start_time"></FormInput>
				</div>
				<div class="flex-grow w-1/2">
					<FormInput label="End time" type="time" v-model="section.end_time"></FormInput>
				</div>
			</div>
			<div class="mt-2 w-full flex flex-row justify-between">
				<div class="flex-grow w-1/2 mr-4">
					<WeatherIconInput :initialIcon="section.weather_icon" @selected="selectWeatherIcon"></WeatherIconInput>
				</div>
				<div class="flex-grow w-1/2">
					<FormInput label="Temperature" type="number" @input="(e) => section.temperature = e" :value="String(section.temperature)"></FormInput>
				</div>
			</div>
			<FileInput @selected="(img) => image = img"></FileInput>

			<a v-if="remove_image && !image" @click="remove_image = false" title="Get image back" class="mt-3 cursor-pointer focus:outline-none focus:text-fade hover:text-fade bg-box hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'undo-alt']"></font-awesome-icon>
			</a>
			<div class="relative">
				<ShowImage v-if="(section.image && !remove_image) || image" :image="section.image" :imagesrc="image" :caption="section.image_caption" class="mt-5 mb-3"></ShowImage>
				<a v-if="!remove_image && !image" @click="remove_image = true" title="Remove the image from this section" class="absolute top-0 right-0 mt-3 mr-3 cursor-pointer focus:outline-none focus:text-fade hover:text-fade bg-box hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center">
					<font-awesome-icon class="text-2xl" :icon="['fas', 'times']"></font-awesome-icon>
				</a>
			</div>
			<FormInput label="Image caption" v-model="section.image_caption"></FormInput>

			<RichTextInput
					:content.sync="section.content"
					:locationables="locationables"
					@selectlocationable="addLocationable"
					@detachlocationable="detachLocationable">
			</RichTextInput>

			<CheckboxInput
					class="mt-10"
					v-if="value.published_at == null || timeIsInFuture(value.published_at)"
					v-model="section.is_draft"
					title="This is a draft">
			</CheckboxInput>
			<CheckboxInput
					v-if="!section.is_draft && (value.published_at == null || timeIsInFuture(value.published_at))"
					v-model="delayed_publishing"
					title="Delay publishing">
			</CheckboxInput>
			<FormInput
					v-if="!section.is_draft && delayed_publishing"
					class="mt-2"
					label="Publish date and time"
					type="datetime-local"
					v-model="section.published_at">
			</FormInput>

			<input @click.prevent="updateSection" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Update this report!">
			<a @click.prevent="goBack" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 text-primary bg-box rounded shadow focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</a>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'
	import FileInput from 'Vue/components/forms/FileInput'
	import CheckboxInput from 'Vue/components/forms/CheckboxInput'
	import WeatherIconInput from 'Vue/components/forms/WeatherIconInput'
	import ShowImage from 'Vue/components/sections/ShowImage'

	export default {
		name: 'edit-section',

		props: {
			value: {
				type: Object,
				default: {},
			},
		},

		components: {
			RichTextInput,
			FormInput,
			FileInput,
			CheckboxInput,
			WeatherIconInput,
			ShowImage,
		},

		data() {
			return {
				section: _.cloneDeep(this.value),
				remove_image: false,
				image: null,
				duration: "--",
				preview: false,
				locationables: _.cloneDeep(this.value.locationables),
				delayed_publishing: this.timeIsInFuture(this.value.published_at) && !this.value.is_draft,

				error: "",
			};
		},

		created() {
			this.section.published_at = this.formatDateTimeInput(this.section.published_at);
		},

		methods: {
			selectWeatherIcon(icon) {
				this.section.weather_icon = icon;
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
				if (this.changed()) {
					this.confirmAlert({
						text: "When you go back, you'll lose your changes to this post!",
						confirmButtonText: 'Yes, go back without saving!',
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
			updateSection: function() {
				this.startLoading();

				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;

				axios.patch(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${this.section.id}`, {
					start_time: this.section.start_time,
					end_time: this.section.end_time,
					weather_icon: this.section.weather_icon,
					temperature: this.section.temperature,
					remove_image: this.remove_image,
					...(this.image !== null) ? {image_file: this.image} : {},
					image_caption: this.section.image_caption,
					content: this.section.content,
					is_draft: this.section.is_draft,
					locationables: this.locationables,
					...(this.delayed_publishing) ? {published_at: this.toUTC(this.section.published_at)} : {},
					visibility: "friends", // TODO
				})
					.then((response) => {
						this.notifyAlert({
							title: "Done!",
							text: "This section has been updated, you rock!",
						});

						this.$emit('input', response.data);
						this.$emit('updated');
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
            	console.log(error.response);
				if (error.response.status == 401) {
					// log out
				}

				this.error = error.response.data;
            },
			changed: function() {
				if (this.value.start_time !== this.section.start_time ||
					this.value.end_time !== this.section.end_time ||
					this.value.content !== this.section.content ||
					this.locationables.length !== this.value.locationables.length) {
					return true;
				} else {
					return false;
				}
			}
		},
	}
</script>
