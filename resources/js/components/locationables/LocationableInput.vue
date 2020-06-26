<template>
	<div class="pb-1/2">
		<MapInputComponent :coordinates="coordinates" :suggestion="suggestion" :zoom="zoom" :disable="creating" class="absolute overflow-hidden w-1/2 h-full" @selected="updateLocation"></MapInputComponent>
		<div class="absolute bg-box h-full p-4 right-0 w-1/2 border-l border-primary overflow-auto">
			<div class="w-full h-full flex flex-col items-center justify-center" v-if="!selected && !hasLocationable">
				<div class="text-3xl text-primary">Select a location on the map</div>
				<div class="mt-4 text-fade-more">Options will be displayed here</div>
			</div>
			<div v-else class="h-full">
				<div v-if="!creating" class="flex flex-col h-full">
					<div @click.prevent="creating = true" class="bg-green-700 cursor-pointer px-4 py-4 rounded text-center text-white">Create a new location at these coordinates</div>
					<div v-if="hasLocationable" @click.prevent="selectLocationable(locationable)" class="bg-box-fade cursor-pointer flex items-center mt-2 rounded text-primary">
						<div class="p-3 rounded-l text-2xl text-white bg-green-700" :class="{'text-primary bg-box-fade': !isSelected(locationable)}">
							<font-awesome-icon :icon="['far', 'square']" v-if="!isSelected(locationable)"></font-awesome-icon>
							<font-awesome-icon :icon="['far', 'check-square']" v-else></font-awesome-icon>
						</div>
						<div class="ml-3">{{ locationable.name }}</div>
					</div>
					<div v-if="loading && this.suggestions.length === 0" class="flex flex-1 items-center justify-center text-fade-more">Loading suggestions...</div>
					<div v-else-if="this.suggestions.length === 0" class="flex flex-1 items-center justify-center text-fade-more">No suggestions for now :(</div>
					<div v-else class="border-t-2 border-box-fade mt-4 pt-2">
						<div v-for="sug in suggestions" :key="sug.id" @click.prevent="selectLocationable(sug)" class="bg-box-fade cursor-pointer flex items-center mt-2 rounded text-primary">
							<div class="p-3 rounded-l text-2xl text-white bg-green-700" :class="{'text-primary bg-box-fade': !isSelected(sug)}">
								<font-awesome-icon :icon="['far', 'square']" v-if="!isSelected(sug)"></font-awesome-icon>
								<font-awesome-icon :icon="['far', 'check-square']" v-else></font-awesome-icon>
							</div>
							<div class="ml-3">{{ sug.name }} ({{ Math.round(sug.distance / 10) / 100 }} km)</div>
						</div>
					</div>
				</div>
				<CreateLocation :position="selectedPosition" @back="creating = false" @created="createdLocation" v-else></CreateLocation>
			</div>
		</div> <!-- Result/input window -->
	</div>
</template>

<script>
	import NProgress from 'nprogress'
	import CreateLocation from 'Vue/components/locationables/CreateLocation'

	export default {
		name: 'locationable-input',

		components: {
			CreateLocation,
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
