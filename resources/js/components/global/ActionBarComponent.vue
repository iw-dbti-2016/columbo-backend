<template>
	<div class="flex justify-between">
		<div class="flex justify-start items-center">
			<a @click.prevent="$emit('back')" v-if="this.showBack"
					class="mr-8 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					:title="this.backTitle">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'arrow-left']" />
			</a>
			<div class="w-20" v-else-if="showOffset"></div>
			<div class="text-primary text-3xl">{{ this.title }}</div>
		</div>
		<div class="flex justify-end items-baseline">
			<a @click.prevent="$root.toggleTheme" v-if="showToggleTheme"
					class="ml-4 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					title="Toggle theme">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'moon']" v-if="$root.theme === 'light-mode'"/>
				<font-awesome-icon class="text-2xl" :icon="['fas', 'sun']" v-else/>
			</a>
			<a @click.prevent="$emit('extraaction')" v-if="this.showExtraAction"
					class="ml-4 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					:title="this.extraActionTitle">
				<font-awesome-icon class="text-2xl" :icon="['fas', this.extraActionIcon]" />
			</a>
			<a @click.prevent="$emit('edit')" v-if="this.showEdit"
					class="ml-4 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					title="Edit">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'pen']" />
			</a>
			<a @click.prevent="$emit('remove')" v-if="this.showRemove"
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
			showBack: {
				type: Boolean,
				default: false,
			},
			backTitle: {
				type: String,
				default: "Back",
			},
			showOffset: {
				type: Boolean,
				default: false,
			},

			title: {
				type: String,
				default: "",
			},

			showToggleTheme: {
				type: Boolean,
				default: false,
			},

			showExtraAction: {
				type: Boolean,
				default: false,
			},
			extraActionTitle: {
				type: String,
				default: "info",
			},
			extraActionIcon: {
				type: String,
				default: "info",
			},

			showEdit: {
				type: Boolean,
				default: false,
			},
			showRemove: {
				type: Boolean,
				default: false,
			},
		},
	}
</script>
