<template>
	<div class="flex flex-col h-full">
		<label class="text-fade mt-3 block" :for="name">{{label}}</label>
		<div class="flex flex-auto flex-col flex-grow flex-shrink justify-center">
			<input v-model="content"
					@input="onInput"
					:name="name"
					:id="name"
					:placeholder="label"
					autocomplete="off"
					spellcheck="false"
					:type="type"
					:class="{'bg-box-fade': fade}"
					class="text-primary w-full block mt-2 px-4 py-3 bg-box shadow rounded focus:outline-none focus:shadow-md">
		</div>
		<div>
			<span v-if="info !== null">{{info}}</span>
			<span v-if="errors !== null">
				<ul>
					<li v-for="error in errors">{{error}}</li>
				</ul>
			</span>
		</div>
	</div>
</template>

<script>
	export default {
		name: 'form-input',

		props: {
			label: {
				type: String,
				default: '',
			},
			value: {
				type: String,
				default: '',
			},
			type: {
				type: String,
				default: 'text',
			},
			info: {
				type: String,
				default: null,
			},
			errors: {
				type: Array,
				default: null,
			},

			fade: {
				type: Boolean,
				default: false,
			},
		},

		data() {
			return {
				content: this.value,
			};
		},

		computed: {
			name: function() {
				return this.label.toLowerCase().replace(/\s/g, "_");
			}
		},

		methods: {
			onInput(e) {
				this.$emit('input', this.content);
			}
		}
	}
</script>
