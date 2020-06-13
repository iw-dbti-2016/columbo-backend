<template>
	<div class="w-full" :title="`This trip is ${Math.round(progress)}% over`">
		<div class="w-full bg-box-fade h-2 rounded-full">
			<div class="h-2 bg-green-600 rounded-full" :style="`width:${this.progress}%`"></div>
		</div>
		<div class="flex justify-between mt-3">
			<div class="text-primary text-lg ml-1">{{this.start_date}}</div>
			<div class="text-primary text-lg mr-1">{{this.end_date}}</div>
		</div>
	</div>
</template>

<script>
	import moment from 'moment'

	export default {
		props: {
			start: {
				type: String
			},
			end: {
				type: String
			},
			current: {
				type: String
			}
		},
		data() {
			return {
				start_date: moment(this.start).format('DD/MM/YYYY'),
				end_date: moment(this.end).format('DD/MM/YYYY'),
				progress: this.cap(moment(this.start).diff(moment(this.current)) / moment(this.start).diff(moment(this.end))),
			};
		},
		create() {
			this.calculateProgress()
		},
		methods: {
			cap(value) {
				return (value > 1) ? 100 : ( (value < 0) ? 0 : value * 100 )
			}
		},
	}
</script>
