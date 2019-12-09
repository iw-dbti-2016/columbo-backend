<template>
	<div class="w-full h-64" id="map"></div>
</template>

<script>
	import 'ol/ol.css';
	import {Map, View, control, interaction} from 'ol';
	import TileLayer from 'ol/layer/Tile';
	import OSM from 'ol/source/OSM';
	import {fromLonLat} from 'ol/proj';
	import {defaults as interactionDefaults} from 'ol/interaction.js';
	import {defaults as controlDefaults} from 'ol/control.js';

	export default {
		props: {
			coordinates: {
				type: Array,
				default: [5,51],
			},
			zoom: {
				type: Number,
				default: 8,
			},
		},
		data() {
			return {
				mapCoordinates: this.coordinates,
				mapZoom: this.zoom,
				map: null,
			};
		},
		mounted() {
			this.map = new Map({
				target: 'map',
				layers: [
					new TileLayer({source: new OSM()})
				],
				view: new View({
					center: fromLonLat(this.mapCoordinates),
					zoom: this.mapZoom,
				}),
				interactions: interactionDefaults({
					doubleClickZoom: false,
					dragAndDrop: false,
					dragPan: false,
					keyboardPan: false,
					keyboardZoom: false,
					mouseWheelZoom: false,
					pointer: false,
					select: false
				}),
				controls: controlDefaults({
					attribution: false,
					zoom: false,
				}),
			})
		},
		watch: {
			mapCoordinates: function(value) {
				this.map.getView().setCenter(fromLonLat(value));
			},
			mapZoom: function(value) {
				this.map.getView().setZoom(value);
			},
			coordinates: function(value) {
				this.map.getView().setCenter(fromLonLat(value));
			},
			zoom: function(value) {
				this.map.getView().setZoom(value);
			}
		}
	}
</script>
