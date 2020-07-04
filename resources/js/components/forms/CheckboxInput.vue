<template>
	<div @click.prevent="toggle" class="bg-box cursor-pointer flex items-center mt-2 rounded text-primary">
		<div class="p-3 rounded-l text-2xl text-white bg-green-700" :class="{'text-primary bg-box': !value}">
			<font-awesome-icon :icon="['far', 'square']" v-if="!value"></font-awesome-icon>
			<font-awesome-icon :icon="['far', 'check-square']" v-else></font-awesome-icon>
		</div>
		<div class="ml-3">{{ title }}</div>
	</div>
</template>

<script>
	export default {
		name: 'checkbox-input',

		props: {
			title: {
				type: String,
				default: '',
			},
			value: {
				type: Boolean,
				default: false,
			},
		},

		methods: {
			toggle() {
				this.$emit('input', !this.value);
			}
		}
	}
</script>
