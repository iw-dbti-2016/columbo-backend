<template>
	<div>
		<ActionBarComponent
				:showBack="true"
				v-on:back="$emit('back')"
				title="New location">
		</ActionBarComponent>
		<FormInput v-model="name" label="Name" :fade="true"></FormInput>
		<div>
			<label class="text-fade mt-3 block" for="Info">Info</label>
			<textarea v-model="info" name="info" id="info" placeholder="Write some info about this location" autocomplete="off" spellcheck="false" class="text-primary w-full block mt-2 px-4 py-3 bg-box-fade shadow rounded focus:outline-none focus:shadow-md"></textarea>
			<div>

			</div>
		</div>
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
		<input @click.prevent="submitLocation" class="block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Create this location!">
	</div>
</template>

<script>
	import NProgress from 'nprogress'
	import Swal from 'sweetalert2'
	import FormInput from 'Vue/components/forms/FormInput'

	export default {
		name: 'create-location',

		components: {
			FormInput,
		},

		props: {
			position: {
				type: Object,
				default: function() {
					return {};
				},
			},
		},

		data() {
			return {
				name: '',
				info: '',
				draft: true,
			}
		},

		methods: {
			submitLocation: function() {
				NProgress.start();

				axios.post(`/api/v1/trips/${this.$route.params.tripId}/locations`, {
					coordinates: this.position.coordinates,
					map_zoom: this.position.map_zoom,
					is_draft: this.draft,
					name: this.name,
					info: this.info,
					visibility: "friends", // TODO
					//published_at for postponed publication
				})
					.then((response) => {
						Swal.fire({
							title: "Done!",
							text: "This location has been created, nice work!",
							icon: "success",
							target: document.getElementById('parent-element'),
						});

						this.$emit('created', response.data);
					})
					.catch((error) => console.log(error));
			},
		},
	}
</script>
