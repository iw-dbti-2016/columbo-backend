<template>
	<div class="w-full">
		<div class="w-full bg-black h-2 rounded-full">
			<div class="h-2 bg-green-800 rounded-full" :style="`width:${this.progress}%`"></div>
		</div>
		<div class="flex justify-between">
			<div class="text-white">{{this.start_date}}</div>
			<div class="text-white">{{this.end_date}}</div>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			start: {
				type: String
			},
			end: {
				type: String
			},
			current: {
				type: Number
			}
		},
		data() {
			return {
				start_date: 0,
				end_date: 0,
				progress: 0.0,
			};
		},
		watch: {
			start: function(value) {
				this.start_date = Date.parse(value)
				this.calculateProgress()
			},
			end: function(value) {
				this.end_date = Date.parse(value)
				this.calculateProgress()
			}
		},
		methods: {
			calculateProgress() {
				let prog = (this.current - this.start_date) / (this.end_date - this.start_date)
				this.progress = (prog > 1) ? 100 : prog * 100
			}
		},
	}
</script>
