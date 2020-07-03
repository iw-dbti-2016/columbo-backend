<template>
	<div>
		<label class="text-fade mt-3 block" for="weather-icon">Weather icon</label>
		<div class="text-primary text-2xl flex justify-around mt-2">
			<label
					v-for="icon in icons"
					class="flex flex-col items-center w-10 p-2 rounded cursor-pointer hover:bg-box"
					:title="titles[icon]"
					:for="`${icon}-radio`"
					:class="{'bg-box cursor-default': weather_icon === icon}">
				<font-awesome-icon :icon="['fas', icon]"></font-awesome-icon>
				<input
						type="radio"
						name="weather-icon"
						class="mt-2 cursor-pointer"
						:id="`${icon}-radio`"
						:value="icon"
						v-model="weather_icon"
						@click="selectIcon(icon)">
			</label>
		</div>
	</div>
</template>

<script>
	export default {
		name: 'weather-icon-input',

		props: {
			initialIcon: {
				type: String,
				default: 'thermometer-half',
			}
		},

		data() {
			return {
				icons: [
					'thermometer-half',
					'sun',
					'cloud-sun',
					'cloud-sun-rain',
					'moon',
					'cloud-moon',
					'cloud-moon-rain',
					'cloud',
					'cloud-showers-heavy'
				],
				titles: {
					'thermometer-half': "General",
					'sun': "It's sunny",
					'cloud-sun': "It's sunny with some clouds",
					'cloud-sun-rain': "It's sunny with some rain",
					'moon': "The moon is visible",
					'cloud-moon': "The moon is visible behind some clouds",
					'cloud-moon-rain': "The moon is visible with some rain",
					'cloud': "It's overcast",
					'cloud-showers-heavy': "It's raining"
				},
				weather_icon: this.initialIcon,
			};
		},

		methods: {
			selectIcon: function(icon) {
				this.$emit('selected', icon);
			}
		},
	}
</script>
