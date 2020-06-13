<template>
	<div class="hover:bg-gray-700 text-gray-100 w-8 h-6 flex items-center justify-center rounded cursor-pointer mr-1" :class="{'bg-gray-700': isActive}" @click="$emit('click')" :title="title">
		<font-awesome-icon :icon="['fa' + this.iconType, this.icon]"/><span class="text-xl font-bold">{{ this.postFix }}</span>
	</div>
</template>

<script>
	export default {
		name: 'editor-menu-button',

		props: {
			iconType: {
				type: String,
				default: 's',
			},
			icon: {
				type: String,
				default: '',
			},
			title: {
				type: String,
				default: '',
			},
			isActive: {
				type: Boolean,
				default: false,
			},
			postFix: {
				type: String,
				default: '',
			},
		}
	}
</script>
