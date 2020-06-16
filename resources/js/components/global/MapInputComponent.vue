<template>
	<div id="map" class="bg-primary"></div>
</template>

<script>
import 'ol/ol.css';
	import {Map, View} from 'ol';
	import OlLayerVector from 'ol/layer/Vector';
	import OlSourceVector from 'ol/source/Vector';
	import OlFeature from 'ol/Feature';
	import TileLayer from 'ol/layer/Tile';
	import OlPoint from 'ol/geom/Point';
	import OSM from 'ol/source/OSM';
	import {fromLonLat,toLonLat} from 'ol/proj';
	import {defaults as interactionDefaults} from 'ol/interaction.js';
	import {defaults as controlDefaults} from 'ol/control.js';
	import Colorize from 'ol-ext/filter/Colorize'

	export default {
		props: {
			coordinates: {
				type: Array,
				default: () => {
					return [0,0]
				},
			},
			zoom: {
				type: Number,
				default: 4,
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

			var vector = new OlLayerVector({
				source: new OlSourceVector(),
			});

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
					doubleClickZoom: true,
					dragAndDrop: true,
					dragPan: true,
					keyboardPan: true,
					keyboardZoom: true,
					mouseWheelZoom: true,
					pointer: true,
					select: true
				}),
				controls: controlDefaults({
					attribution: false,
					zoom: true,
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

			this.map.on('click', (evt) => {
				this.map.getView().setCenter(evt.coordinate);

				this.map.getLayers().array_.forEach((item,_) => {
					if (item instanceof OlLayerVector) {
						this.map.removeLayer(item)
					}
				});

				let m = new OlLayerVector({
					source: new OlSourceVector({
						features: [
							new OlFeature({
								geometry: new OlPoint(evt.coordinate)
							})
						]
					})
				});
				this.map.addLayer(m);

				this.$emit('selected', {'coordinates': toLonLat(evt.coordinate), 'map_zoom': this.map.getView().getZoom()})
			});

			this.filter = new Colorize();
			layer.addFilter(this.filter);

			// https://viglino.github.io/ol-ext/examples/filter/map.filter.colorize.html
			this.filter.setFilter({operation: 'difference', red:215, green: 255, blue: 255, value: 1});
			this.filter.setActive(this.$root.theme !== 'light-mode')
		},
		watch: {
			'$root.theme': function(value) {
				this.filter.setActive(value !== 'light-mode')
			}
		}
	}
</script>
