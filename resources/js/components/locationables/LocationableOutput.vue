<template>
	<div v-if="showLocationable !== null" :class="{'pb-1/3': locationableType === 'location'}" class="relative w-full pb-1/2 mt-6 shadow-lg rounded-lg overflow-hidden">
		<div>
			<MapOutputComponent class="absolute rounded-l-lg overflow-hidden w-1/3 h-full" :zoom="Math.max(0, showLocationable.map_zoom - extraZoom)" :coordinates="showLocationable.coordinates"></MapOutputComponent>
			<div class="absolute right-0 top-0 w-2/3 bg-box rounded-r-lg h-full py-6 px-8 text-primary overflow-auto">
				<div v-if="locationableType === 'poi'" class="flex text-3xl mb-4 items-end">
					<font-awesome-icon class="text-yellow-600" :icon="['fas', 'star']" title="Point of Interest"></font-awesome-icon>
					<div class="ml-2">Point of Interest</div>
				</div>

				<div class="text-3xl font-bold leading-8 font-serif">{{ showLocationable.name }}</div>
				<img v-if="locationableType === 'poi'" class="rounded-lg mt-4 w-full" src="http://via.placeholder.com/500x250" alt="#">
				<div v-if="locationableType === 'poi'">{{ showLocationable.image_caption }}</div>
				<div class="mt-4 leading-6 font-serif text-justify">{{ showLocationable.info }}</div>

				<div v-if="locationableType === 'location'">
					<div v-if="showLocationable.is_draft" class="absolute bg-green-500 px-4 py-2 right-0 rounded-bl-lg rounded-tr-lg text-white top-0">DRAFT</div>
					<div class="mt-4">{{ humanTimeDiff(showLocationable.published_at) }}</div>
					<div class="mt-4">{{ showLocationable.visibility }}</div>
					<div class="">{{ showLocationable.user_id }}</div>
				</div>
			</div>
		</div>
		<div class="absolute bg-box-fade bottom-0 flex items-center justify-center left-0 mb-2 ml-2 rounded-md text-2xl p-2">
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
			locationables: {
				type: Array,
				default: null,
			},
			locationableid: {
				type: String,
				default: "",
			},
			locationabletype: {
				type: String,
				default: "",
			},
		},

		created() {
			if (this.locationabletype !== '' && this.locationableid !== '') {
				for (var i = this.locationables.length - 1; i >= 0; i--) {
					if (this.locationables[i].type == this.locationabletype) {
						if (this.locationables[i].id == this.locationableid) {
							this.showLocationable = this.locationables[i];
							break;
						}
					}
				}
			}
			console.log(this.showLocationable);
		},

		data() {
			return {
				showLocationable: this.locationable,
				extraZoom: 0,
				showLocationableImage: false,
			};
		},

		computed: {
			locationableType: function() {
				if (this.locationabletype !== "") {
					return this.locationabletype;
				}

				return this.showLocationable.type;
			}
		},

		watch: {
			locationable: function(value) {
				this.showLocationable = value;
			}
		}
	}
</script>
