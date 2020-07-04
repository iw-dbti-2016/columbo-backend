<template>
	<div class="pb-1/2">
		<MapInputComponent :coordinates="coordinates" :suggestion="suggestion" :zoom="zoom" :disable="creating" class="absolute overflow-hidden w-1/2 h-full" @selected="updateLocation"></MapInputComponent>
		<div class="absolute bg-primary h-full right-0 w-1/2 border-l border-primary">
			<div class="w-full h-full flex flex-col items-center justify-center" v-if="!selected && !hasLocationable">
				<div class="text-3xl text-primary">Select a location on the map</div>
				<div class="mt-4 text-fade-more">Options will be displayed here</div>
			</div>
			<div v-else class="h-full">
				<div v-if="!creating" class="flex flex-col h-full">
					<div class="flex px-4 pt-4">
						<div @click.prevent="creating = true" class="flex-grow bg-green-700 cursor-pointer px-4 py-4 rounded text-center text-white">Create a new location at these coordinates</div>
						<a @click.prevent="$emit('quit')"
								class="ml-2 cursor-pointer focus:outline-none focus:text-fade hover:text-fade bg-box hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
								title="Cancel">
							<font-awesome-icon class="text-2xl" :icon="['fas', 'times']" />
						</a>
					</div>
					<CheckboxInput
							v-if="hasLocationable"
							@input="selectLocationable(locationable)"
							:value="isSelected(locationable)"
							:title="locationable.name">
					</CheckboxInput>

					<div v-if="loading && this.suggestions.length === 0" class="flex flex-1 items-center justify-center text-fade-more">Loading suggestions...</div>
					<div v-else-if="this.suggestions.length === 0" class="flex flex-1 items-center justify-center text-fade-more">No suggestions for now :(</div>
					<div v-else class="relative border-t-2 border-box-fade mt-4 overflow-auto px-4 pb-4">
						<div class="bg-primary sticky top-0 pt-4">
							<a @click.prevent="$emit('insert')" class="block bg-green-700 cursor-pointer p-4 rounded text-center text-white">Select this location</a>
						</div>

						<CheckboxInput
								v-for="sug in suggestions" :key="sug.id"
								@input="selectLocationable(sug)"
								:value="isSelected(sug)"
								:title="`${sug.name} (${Math.round(sug.distance / 10) / 100} km)`">
						</CheckboxInput>
					</div>
				</div>
				<CreateLocation class="p-4 max-h-full overflow-auto" :position="selectedPosition" @back="creating = false" @created="createdLocation" v-else></CreateLocation>
			</div>
		</div>
	</div>
</template>

<script>
	import NProgress from 'nprogress'
	import CreateLocation from 'Vue/components/locationables/CreateLocation'
	import CheckboxInput from 'Vue/components/forms/CheckboxInput'

	export default {
		name: 'locationable-input',

		components: {
			CreateLocation,
			CheckboxInput,
		},

		props: {
			suggestion: {
				type: Object,
				default: function() {
					return {
						"longitude": 0,
						"latitude": 0,
					};
				},
			},
			locationable: {
				type: Object,
				default: function() {
					return {};
				},
			},
		},

		data() {
			return {
				suggestions: [],
				selectedPosition: null,
				selectedLocationable: null,
				loading: true,
				selected: false,
				creating: false,
			};
		},

		created() {
			if (this.$options.propsData.hasOwnProperty('locationable') && this.hasLocationable) {
				this.selectedLocationable = this.locationable;
				this.updateLocation({"coordinates": this.locationable.coordinates});
			}
		},

		methods: {
			createdLocation: function(e) {
				e.location.distance = 0;

				this.suggestions.unshift(e);
				this.selectLocationable(e);

				this.creating = false;

				NProgress.done();
			},
			updateLocation: function(e) {
				NProgress.start();

				this.selectedPosition = e,
				this.selected = true;
				this.loading = true;

				axios.post(`/api/v1/trips/${this.$route.params.tripId}/locationables`, e)
					.then((response) => {
						this.suggestions = response.data;
						this.loading = false;

						NProgress.done();
					})
					.catch((error) => console.log(error));
			},
			selectLocationable: function(locationable) {
				if (this.isSelected(locationable)) {
					this.selectedLocationable = null;

					this.$emit('selectlocationable', {});
				} else {
					this.selectedLocationable = locationable;

					if (this.locationableEquals(this.locationable, locationable)) {
						this.$emit('selectlocationable', null);
					} else {
						this.$emit('selectlocationable', {
							"type": this.selectedLocationable.type,
							"id": this.selectedLocationable.id,
							"name": this.selectedLocationable.name,
						});
					}
				}
			},
			isSelected: function(locationable) {
				return this.locationableEquals(locationable, this.selectedLocationable);
			},
			locationableEquals: function(l1, l2) {
				let l1Type = this.getType(l1);
				let l2Type = this.getType(l2);

				return (l1Type && l2Type && l1Type === l2Type && l1.id === l2.id);
			},
			getType: function(locationable) {
				if (locationable === null || Object.keys(locationable).length === 0) {
					return false;
				}

				return locationable.type;
			}
		},

		computed: {
			coordinates: function() {
				if (this.locationable !== null && Object.keys(this.locationable).length !== 0) {
					return this.locationable.coordinates;
				} else {
					return {};
				}
			},
			zoom: function() {
				if (this.locationable !== null && Object.keys(this.locationable).length !== 0) {
					return this.locationable.map_zoom;
				} else {
					return this.$options.propsData.hasOwnProperty('suggestion') ? 6 : 2;
				}
			},
			hasLocationable: function() {
				return this.locationable !== null && Object.keys(this.locationable).length !== 0;
			},
			locationableType: function() {
				return this.hasLocationable && this.locationable.hasOwnProperty('location') ? 'location' : 'poi';
			}
		},
	}
</script>
