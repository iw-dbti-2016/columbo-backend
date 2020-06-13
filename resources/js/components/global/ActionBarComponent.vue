<template>
	<div class="mt-4 flex justify-between">
		<div class="flex justify-start items-center">
			<router-link :to="this.backLink" v-if="this.backLink != null"
					class="mr-8 focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					:title="this.backTitle">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'arrow-left']" />
			</router-link>
			<div class="w-20" v-else></div>
			<div class="text-primary text-3xl">{{ this.title }}</div>
		</div>
		<div class="flex justify-end items-baseline">
			<a @click="$root.toggleTheme"
					class="ml-4 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					title="Toggle theme">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'moon']" v-if="$root.theme === 'light-mode'"/>
				<font-awesome-icon class="text-2xl" :icon="['fas', 'sun']" v-else/>
			</a>
			<a v-if="this.showExtraActionLink" @click.prevent="$emit('extraactionclick')"
					class="ml-4 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					:title="this.extraActionTitle">
				<font-awesome-icon class="text-2xl" :icon="['fas', this.extraActionIcon]" />
			</a>
			<router-link :to="this.editLink" v-if="this.editLink != null"
					class="ml-4 focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					title="Edit">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'pen']" />
			</router-link>
			<a v-if="this.showRemoveLink" @click.prevent="$emit('removeclick')"
					class="ml-4 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
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
				default: false,
			},
			showExtraActionLink: {
				type: Boolean,
				default: false,
			},
			extraActionIcon: {
				type: String,
				default: "info",
			},
			extraActionTitle: {
				type: String,
				default: "info",
			},
			title: {
				type: String,
				default: "",
			},
		},
	}
</script>
