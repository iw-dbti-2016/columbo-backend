<template>
	<div v-if="locationable !== null" class="relative w-full pb-1/2">
		<keep-alive>
			<MapOutputComponent class="absolute rounded-lg overflow-hidden w-full h-full" v-if="showMap" :zoom="locationable[locationableType].map_zoom" :coordinates="locationable[locationableType].coordinates"></MapOutputComponent>
			<img v-else class="absolute rounded-lg overflow-hidden object-cover w-full h-full" src="http://via.placeholder.com/500x250" alt="#">
		</keep-alive>
		<div class="absolute bg-box-fade flex flex-col items-center mr-2 mt-2 px-1 px-2 py-3 right-0 rounded-full text-2xl top-0">
			<font-awesome-icon @click="showMap = true" :class="{'cursor-pointer text-fade-more hover:text-primary': !showMap, 'text-primary': showMap}" class="cursor-default" :icon="['fas', 'map-marker-alt']" />
			<font-awesome-icon @click="showMap = false" :class="{'cursor-pointer text-fade-more hover:text-primary': showMap, 'text-primary': !showMap}" class="mt-2 cursor-default" :icon="['far', 'image']" />
		</div>
	</div>
</template>

<script>
	export default {
		name: 'show-locationable',

		props: {
			'locationable': {
				type: Object,
				default: null,
			},
		},

		data() {
			return {
				showMap: true,
			};
		},

		computed: {
			locationableType: function() {
				return this.locationable !== null && this.locationable.hasOwnProperty('location') ? 'location' : 'poi';
			}
		},
	}
</script>
