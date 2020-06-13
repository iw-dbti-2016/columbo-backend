<template>
	<div class="mt-16 bg-box rounded-lg shadow-lg overflow-hidden">
		<div v-if="section !== null">
			<div class="px-6 py-4 relative text-primary">
				<span class="text-4xl">{{ section.start_time }}</span>
				<span class="absolute top-0 right-0 mt-4 mr-5 text-4xl">{{ calculateDuration() }}</span>
			</div>
			<div class="relative" v-if="section.locationable != null">
				<keep-alive>
					<MapOutputComponent v-if="showMap" :zoom="section.locationable[locType].map_zoom" :coordinates="section.locationable[locType].coordinates"></MapOutputComponent>
					<img v-else src="http://via.placeholder.com/500x250" alt="#">
				</keep-alive>
				<div class="absolute bg-box-fade flex flex-col items-center mr-2 mt-2 px-1 px-2 py-3 right-0 rounded-full text-2xl top-0">
					<font-awesome-icon @click="showMap = true" :class="{'cursor-pointer text-fade-more hover:text-primary': !showMap, 'text-fade': showMap}" class="cursor-default" :icon="['fas', 'map-marker-alt']" />
					<font-awesome-icon @click="showMap = false" :class="{'cursor-pointer text-fade-more hover:text-primary': showMap, 'text-fade': !showMap}" class="mt-2 cursor-default" :icon="['far', 'image']" />
				</div>
			</div>
		</div>
		<span class="block mt-2 text-fade-more px-6 py-4" v-else-if="loading">Loading sections.</span>
		<!-- <span class="block mt-2 text-gray-700 px-6 py-4" v-else>When adding sections, location data will be shown here</span> -->
	</div>
</template>

<script>
	export default {
		props: {
			section: {
				type: Object,
				default: null,
			},
			loading: {
				type: Boolean,
				default: true,
			}
		},
		data() {
			return {
				showMap: true,
				locType: 'location',
			};
		},
		create() {
			if (this.section.locationable != null && this.section.locationable.hasOwnProperty('location')) {
				this.locType = 'location'
			}
		},
		methods: {
			calculateDuration: function() {
				if (this.section.start_time === null || this.section.end_time === null) {
					return "0m";
				}

				if (typeof this.section.start_time === "undefined" || typeof this.section.end_time === "undefined") {
					return "0m";
				}

				let start = this.section.start_time.split(":");
				let end = this.section.end_time.split(":");

				start = 60 * parseInt(start[0]) + parseInt(start[1]);
				end = 60 * parseInt(end[0]) + parseInt(end[1]);

				let duration = end - start;

				let hr = Math.floor(duration / 60);
				let min = duration % 60;

				let formatted_duration = "";

				if (hr != 0) {
					formatted_duration += hr + "h";
				}

				if (min != 0) {
					formatted_duration += (min < 10 ? "0" : "") + min;

					if (hr == 0) formatted_duration += "m";
				} else {
					formatted_duration += "00";
				}

				return formatted_duration;
			}
		}
	}
</script>
