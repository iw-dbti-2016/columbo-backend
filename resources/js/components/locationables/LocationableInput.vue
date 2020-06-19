<template>
	<div class="relative w-full pb-1/2 mt-6">
		<MapInputComponent :coordinates="coordinates" :suggestion="suggestion" :zoom="zoom" class="absolute rounded-l-lg overflow-hidden w-1/2 h-full" @selected="updateLocation"></MapInputComponent>
		<div class="absolute bg-box h-full p-4 right-0 rounded-r-lg w-1/2 border-l border-primary">
			<div class="w-full h-full flex flex-col items-center justify-center" v-if="!selected && !hasLocationable">
				<div class="text-3xl text-primary">Select a location on the map</div>
				<div class="mt-4 text-fade-more">Options will be displayed here</div>
			</div>
			<div v-else class="flex flex-col h-full">
				<div class="bg-green-700 cursor-pointer px-4 py-4 rounded text-center text-white">Create a new location at these coordinates</div>
				<div v-if="hasLocationable" @click.prevent="selectLocationable(locationable)" class="bg-box-fade cursor-pointer flex items-center mt-2 rounded text-primary">
					<div class="p-3 rounded-l text-2xl text-white bg-green-700" :class="{'text-primary bg-box-fade': !isSelected(locationable)}">
						<font-awesome-icon :icon="['far', 'square']" v-if="!isSelected(locationable)"></font-awesome-icon>
						<font-awesome-icon :icon="['far', 'check-square']" v-else></font-awesome-icon>
					</div>
					<div class="ml-3">{{ locationable[locationableType].name }}</div>
				</div>
				<div v-if="loading && this.suggestions.length === 0" class="flex flex-1 items-center justify-center text-fade-more">Loading suggestions...</div>
				<div v-else-if="this.suggestions.length === 0" class="flex flex-1 items-center justify-center text-fade-more">No suggestions for now :(</div>
				<div v-else class="border-t-2 border-box-fade mt-4 pt-2">
					<div v-for="sug in suggestions" :key="sug[getType(sug)].id" @click.prevent="selectLocationable(sug)" class="bg-box-fade cursor-pointer flex items-center mt-2 rounded text-primary">
						<div class="p-3 rounded-l text-2xl text-white bg-green-700" :class="{'text-primary bg-box-fade': !isSelected(sug)}">
							<font-awesome-icon :icon="['far', 'square']" v-if="!isSelected(sug)"></font-awesome-icon>
							<font-awesome-icon :icon="['far', 'check-square']" v-else></font-awesome-icon>
						</div>
						<div class="ml-3">{{ sug[getType(sug)].name }} ({{ Math.round(sug[getType(sug)].distance / 10) / 100 }} km)</div>
					</div>
				</div>
			</div>
		</div> <!-- Result/input window -->
	</div>
</template>

<script>
	import NProgress from 'nprogress'

	export default {
		name: 'locationable-input',

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
				selectedLocationable: null,
				loading: true,
				selected: false,
			};
		},

		created() {
			if (this.$options.propsData.hasOwnProperty('locationable') && this.hasLocationable) {
				this.selectedLocationable = this.locationable;
				this.updateLocation({"coordinates": this.locationable[this.locationableType].coordinates});
			}
		},

		methods: {
			updateLocation: function(e) {
				NProgress.start();

				this.selected = true;
				this.loading = true;

				// Implementation:
				// 	Selecting POI or location emits it as chosen location and indicator in this component
				// 	Add new location show form with data:
				// 		- name
				// 		- info
				// 		- is_draft
				// 		- visibility
				// 		- published_at

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
							"type": this.getType(this.selectedLocationable),
							"id": this.selectedLocationable[this.getType(this.selectedLocationable)].id,
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

				return (l1Type && l2Type && l1Type === l2Type && l1[l1Type].id === l2[l2Type].id);
			},
			getType: function(locationable) {
				if (locationable === null || Object.keys(locationable).length === 0) {
					return false;
				}

				return locationable.hasOwnProperty('location') ? 'location' : 'poi';
			}
		},

		computed: {
			coordinates: function() {
				if (this.locationable !== null && Object.keys(this.locationable).length !== 0) {
					return this.locationable[this.locationableType].coordinates;
				} else {
					return {};
				}
			},
			zoom: function() {
				if (this.locationable !== null && Object.keys(this.locationable).length !== 0) {
					return this.locationable[this.locationableType].map_zoom;
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
