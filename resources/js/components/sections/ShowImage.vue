<template>
	<div v-if="image || imagesrc" class="relative w-full pb-2/3 shadow-lg rounded-lg overflow-hidden bg-box">
		<div class="absolute h-full w-full">
			<div class="absolute justify-center left-0 top-0 w-full h-full text-center flex items-center italic text-xl text-primary">Loading image...</div>
			<img class="relative w-full h-full object-center" :class="{'pb-10': caption, 'object-cover': !showFullImage, 'object-contain': showFullImage}" :src="imagesource" :alt="caption">
			<div v-if="caption" class="absolute text-primary bg-box font-serif text-xl w-full bottom-0 h-10 px-4 flex items-center">
				<span class="truncate py-1">{{ caption }}</span>
			</div>
		</div>
		<div v-if="showResizeButton" class="absolute bg-box-fade bottom-0 flex items-center justify-center left-0 mb-2 ml-2 rounded-md text-2xl p-2" :class="{'mb-12': caption}">
			<font-awesome-icon v-if="!showFullImage" @click="showFullImage = true" class="cursor-pointer text-fade-more hover:text-primary" :icon="['fas', 'expand']" title="Show the full image" />
			<font-awesome-icon v-else @click="showFullImage = false" class="cursor-pointer text-fade-more hover:text-primary" :icon="['fas', 'compress']" title="Zoom the image to cover" />
		</div>
	</div>
</template>

<script>
	export default {
		name: 'show-image',

		props: {
			image: {
				type: String,
				default: null,
			},
			imagesrc: {
				type: String,
				default: null,
			},
			caption: {
				type: String,
				default: null,
			},
			showResizeButton: {
				type: Boolean,
				default: true,
			}
		},

		data() {
			return {
				showFullImage: false,
			};
		},

		computed: {
			imagesource: function() {
				return (this.imagesrc) ? this.imagesrc : `/storage/section-img/${this.image}_w1k.png`;
			}
		}
	}
</script>
