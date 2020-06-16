<template>
	<div>
		<ActionBarComponent
				:showBack="true"
				v-on:back="$emit('back')"
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
			<span class="text-fade">Duration: {{ section.duration_minutes }}</span>
			<RichTextInput :content.sync="section.content"></RichTextInput>
			<div>
				<label class="text-primary mt-3 block" for="draft">
					<input v-model="section.is_draft" name="draft" id="draft" class="inline-block mt-2 px-4 py-3" type="checkbox">
					<span>This is a draft</span>
				</label>
				<div>
					<span></span>
					<span></span>
				</div>
			</div>
			<input @click.prevent="updateSection" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Update this report!">
			<router-link :to="{name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 text-primary bg-box rounded shadow focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</router-link>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'

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
		},

		data() {
			return {
				section: this.value,
				duration: "--",
				preview: false,

				error: "",
			};
		},

		methods: {
			updateSection: function() {
				let tripId = this.$route.params.tripId;
				let reportId = this.$route.params.reportId;
				let sectionId = this.$route.params.sectionId;

				axios.patch(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${sectionId}`, {
					start_time: this.section.start_time,
					end_time: this.section.end_time,
					content: this.section.content,
					is_draft: this.section.is_draft,
					visibility: "friends", // TODO
					//published_at for postponed publication
				})
					.then((response) => {
						this.$emit('input', this.section);
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
