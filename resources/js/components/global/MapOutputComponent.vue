<template>
	<div id="map" class="bg-primary"></div>
</template>

<script>
	import 'ol/ol.css';
	import {Map, View, control, interaction} from 'ol';
	import OlLayerVector from 'ol/layer/Vector';
	import OlSourceVector from 'ol/source/Vector';
	import OlFeature from 'ol/Feature';
	import TileLayer from 'ol/layer/Tile';
	import OlPoint from 'ol/geom/Point';
	import OSM from 'ol/source/OSM';
	import {fromLonLat} from 'ol/proj';
	import {defaults as interactionDefaults} from 'ol/interaction.js';
	import {defaults as controlDefaults} from 'ol/control.js';
	import Colorize from 'ol-ext/filter/Colorize'

	export default {
		props: {
			coordinates: {
				type: Array,
				default: () => {
					return [5,51]
				},
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
				filter: null,
			};
		},
		mounted() {
			let layer = new TileLayer({source: new OSM()})

			this.map = new Map({
				target: 'map',
				layers: [
					layer
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

			let marker = new OlLayerVector({
				source: new OlSourceVector({
					features: [
						new OlFeature({
							geometry: new OlPoint(fromLonLat(this.mapCoordinates))
						})
					]
				})
			});
			this.map.addLayer(marker);

			this.filter = new Colorize();
			layer.addFilter(this.filter);

			// https://viglino.github.io/ol-ext/examples/filter/map.filter.colorize.html
			this.filter.setFilter({operation: 'difference', red:215, green: 255, blue: 255, value: 1});
			this.filter.setActive(this.$root.theme !== 'light-mode')
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
			},
			'$root.theme': function(value) {
				this.filter.setActive(value !== 'light-mode')
			}
		}
	}
</script>
