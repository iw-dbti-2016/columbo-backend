<template>
	<div v-if="error.length" class="fixed bg-red-600 px-4 py-4 rounded bottom-0 mb-4 left-0 ml-4 max-w-xs">
		<h1 class="font-bold">Whoops, something went wrong</h1>
		<p class="mt-2">{{ error }}</p>
	</div>
</template>

<script>
	export default {
		props: {
			error: {
				type: String,
				default: "",
			},
			length: {
				type: Number,
				default: 5,
			}
		},
		data() {
			return {
				timeout: null,
			};
		},
		watch: {
			error: function(value) {
				if (this.timeout !== null) {
					clearTimeOut(this.timeout);
				}

				this.timeout = setTimeout(this.hide, this.length * 1000);
			}
		},
		methods: {
			hide() {
				this.error = "";
			},
		},
	};
</script>
