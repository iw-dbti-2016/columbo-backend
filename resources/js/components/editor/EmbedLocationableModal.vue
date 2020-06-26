<template>
	<!-- <div class="modal fixed top-0 left-0 bg-primary w-full h-screen z-50 flex items-center justify-center"> -->
		<div class="modal fixed top-0 left-0 h-screen w-full z-50" v-if="show">
			<a @click.prevent="show=false"
					class="absolute top-0 right-0 z-50 mr-2 mt-2 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					title="Cancel">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'times']" />
			</a>
			<LocationableInput v-on:selectlocationable="(e) => selectedLocationable = e"></LocationableInput>
			<a @click.prevent="insertLocationable"
					class="absolute bottom-0 right-0 z-50 mr-2 mb-2 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
					title="Insert">
				<font-awesome-icon class="text-2xl" :icon="['fas', 'check']" />
			</a>
		</div>
	<!-- </div> -->
</template>

<script>
	import LocationableInput from 'Vue/components/locationables/LocationableInput'

	export default {
		name: 'embed-locationable-modal',

		components: {
			LocationableInput,
		},

		data() {
			return {
				command: null,
				show: false,
				selectedLocationable: null,
			};
		},

		methods: {
			showModal(command) {
				this.command = command;
				this.show = true;
			},
			insertLocationable() {
				if (this.selectedLocationable === null || this.selectedLocationable === {}) {
					return;
				}

				console.log(this.selectedLocationable);

				const data = {
					command: this.command,
					data: {
						title: this.selectedLocationable.name,
						type: this.selectedLocationable.type,
						id: this.selectedLocationable.id,
					}
				};

				this.$emit("onConfirm", data);
				this.show = false;
				this.selectedLocationable = null;
			}
		}
	};
</script>
