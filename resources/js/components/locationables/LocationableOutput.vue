<template>
	<div v-if="locationable !== null" class="relative w-full pb-1/2 mt-6">
		<keep-alive>
			<div v-if="showMap">
				<MapOutputComponent class="absolute rounded-l-lg overflow-hidden w-1/3 h-full" :zoom="Math.max(0, locationable[locationableType].map_zoom - extraZoom)" :coordinates="locationable[locationableType].coordinates"></MapOutputComponent>
				<div class="absolute right-0 w-2/3 bg-box rounded-r-lg h-full py-6 px-8 text-primary overflow-auto">
					<div v-if="locationableType === 'poi'" class="flex text-3xl mb-4 items-end">
						<font-awesome-icon class="text-yellow-600" :icon="['fas', 'star']" title="Point of Interest"></font-awesome-icon>
						<div class="ml-2">Point of Interest</div>
					</div>

					<div class="text-3xl font-bold leading-8 font-serif">{{ locationable[locationableType].name }}</div>
					<img v-if="locationableType === 'poi'" class="rounded-lg mt-4 w-full" src="http://via.placeholder.com/500x250" alt="#">
					<div class="mt-4 leading-6 font-serif text-justify">{{ locationable[locationableType].info }}</div>

					<div v-if="locationableType === 'location'">
						<div v-if="locationable[locationableType].is_draft" class="absolute bg-green-500 px-4 py-2 right-0 rounded-bl-lg rounded-tr-lg text-white top-0">DRAFT</div>
						<div class="mt-4">{{ humanTimeDiff(locationable[locationableType].published_at) }}</div>
						<div class="mt-4">{{ locationable[locationableType].visibility }}</div>
						<div class="">{{ locationable[locationableType].user_id }}</div>
					</div>
				</div>
			</div>
			<img v-else class="absolute rounded-lg overflow-hidden object-cover w-full h-full" src="http://via.placeholder.com/500x250" alt="#">
		</keep-alive>
		<div class="absolute bg-box-fade flex flex-col items-center ml-2 mt-2 px-2 py-3 left-0 rounded-full text-2xl top-0">
			<font-awesome-icon @click="showMap = true" :class="{'cursor-pointer text-fade-more hover:text-primary': !showMap, 'text-primary': showMap}" class="cursor-default" :icon="['fas', 'map-marker-alt']" title="Show the location" />
			<font-awesome-icon @click="showMap = false" :class="{'cursor-pointer text-fade-more hover:text-primary': showMap, 'text-primary': !showMap}" class="mt-2 cursor-default" :icon="['far', 'image']" title="Show the photo" />
		</div>
		<div class="absolute bg-box-fade bottom-0 flex items-center justify-center left-0 mb-2 ml-2 rounded-md text-2xl p-2" v-if="showMap">
			<font-awesome-icon v-if="extraZoom === 0" @click="extraZoom = 2" class="cursor-pointer text-fade-more hover:text-primary" :icon="['fas', 'expand']" title="Wider view" />
			<font-awesome-icon v-else @click="extraZoom = 0" class="cursor-pointer text-fade-more hover:text-primary" :icon="['fas', 'compress']" title="Closer view" />
		</div>
	</div>
</template>

<script>
	export default {
		name: 'locationable-output',

		props: {
			locationable: {
				type: Object,
				default: null,
			},
		},

		data() {
			return {
				extraZoom: 0,
				showMap: true,
				showLocationableImage: false,
			};
		},

		computed: {
			locationableType: function() {
				return this.locationable !== null && this.locationable.hasOwnProperty('location') ? 'location' : 'poi';
			}
		},
	}
</script>
