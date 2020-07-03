<template>
	<div class="mt-1 pt-1">
		<label for="file" class="text-fade mt-3 block">Image</label>
		<div class="relative">
			<input @change="onFileChange" type="file" name="file" id="file" accept="image/*" class="cursor-pointer text-primary w-full block mt-2 px-4 py-3 bg-box shadow rounded focus:outline-none focus:shadow-md">
			<a @click.prevent="clearFile" title="Clear this field" class="absolute text-fade-more hover:text-primary h-full cursor-pointer flex items-center px-4 text-2xl right-0 bottom-0 block">
				<font-awesome-icon :icon="['fas', 'times']"></font-awesome-icon>
			</a>
		</div>
	</div>
</template>

<script>
	export default {
		name: 'file-input',

		data() {
			return {
				image: '',
			}
		},

		methods: {
			onFileChange(e) {
				let files = e.target.files || e.dataTransfer.files;

				if (!files.length) {
					this.$emit('selected', null);
					return;
				}

				this.createImage(files[0]);
			},
			createImage(file) {
				let reader = new FileReader();

				reader.onload = (e) => {
					this.image = e.target.result;
					this.$emit('selected', this.image)
				}

				reader.readAsDataURL(file);
			},
			clearFile() {
				document.getElementById('file').value = '';
				this.image = null;
				this.$emit('selected', this.image);
			},
		},
	}
</script>
