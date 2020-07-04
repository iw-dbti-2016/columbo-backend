<template>
	<div class="modal fixed top-0 left-0 h-screen w-full z-50" v-if="show">
		<LocationableInput
			@selectlocationable="(e) => selectedLocationable = e"
			@quit="show = false"
			@insert="insertLocationable">
		</LocationableInput>
	</div>
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
