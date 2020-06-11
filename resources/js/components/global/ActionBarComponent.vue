<template>
	<div class="mt-5 flex justify-between">
		<div class="flex justify-start items-center">
			<router-link :to="this.backLink" v-if="this.backLink != null"
					class="mr-8 focus:outline-none focus:text-gray-300 hover:text-gray-300 hover:bg-gray-800 bg-gray-900 w-12 h-12 rounded-full text-gray-400 flex items-center justify-center"
					:title="this.backTitle">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'arrow-left']" />
			</router-link>
			<div class="w-20" v-else></div>
			<div class="text-gray-200 text-3xl">{{ this.title }}</div>
		</div>
		<div class="flex justify-end items-baseline">
			<router-link :to="this.editLink" v-if="this.editLink != null"
					class="focus:outline-none focus:text-gray-300 hover:text-gray-300 hover:bg-gray-800 bg-gray-900 w-12 h-12 rounded-full text-gray-400 flex items-center justify-center"
					title="Edit">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'pen']" />
			</router-link>
			<a v-if="this.showRemoveLink" @click="$emit('removeclick')"
					class="ml-4 cursor-pointer focus:outline-none focus:text-gray-300 hover:text-gray-300 hover:bg-gray-800 bg-gray-900 w-12 h-12 rounded-full text-gray-400 flex items-center justify-center"
					title="Remove">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'trash-alt']" />
			</a>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			backLink: {
				type: Object,
				default: null,
			},
			backTitle: {
				type: String,
				default: "Back",
			},
			editLink: {
				type: Object,
				default: null,
			},
			removeLink: {
				type: Object,
				default: null,
			},
			showRemoveLink: {
				type: Boolean,
				defaul: false,
			},
			title: {
				type: String,
				default: "",
			},
		},
	}
</script>
