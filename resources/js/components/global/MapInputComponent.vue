<template>
	<div :id="`map-${mapId}`" class="bg-primary"></div>
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
	import {defaults as controlDefaults, Zoom} from 'ol/control.js';
	import Colorize from 'ol-ext/filter/Colorize'

	export default {
		props: {
			coordinates: {
				type: Object,
				default: function() {
					return {};
				},
			},
			suggestion: {
				type: Object,
				default: function() {
					return {
						"longitude": 0,
						"latitude": 0,
					};
				},
			},
			zoom: {
				type: Number,
				default: 2,
			},
			disable: {
				type: Boolean,
				default: false,
			},
		},
		data() {
			return {
				mapCoordinates: this.toLonLat(this.coordinates),
				mapSuggestion: this.toLonLat(this.suggestion),
				mapZoom: this.zoom,
				map: null,
				interactions: null,
				controls: null,
				filter: null,
				selectedPoint: null,
				mapId: Math.floor(Math.random() * Math.floor(1000000000)),
			};
		},
		mounted() {
			let layer = new TileLayer({source: new OSM()})

			var vector = new OlLayerVector({
				source: new OlSourceVector(),
			});

			this.interactions = interactionDefaults({
				doubleClickZoom: true,
				dragAndDrop: true,
				dragPan: true,
				keyboardPan: true,
				keyboardZoom: true,
				mouseWheelZoom: true,
				pointer: true,
				select: true
			});

			this.controls = controlDefaults({
				attribution: true,
				zoom: true,
			});

			this.map = new Map({
				target: 'map-' + this.mapId,
				layers: [
					layer
				],
				view: new View({
					center: fromLonLat(this.mapCoordinates),
					zoom: this.mapZoom,
				}),
				interactions: this.interactions,
				controls: this.controls,
			})

			if (this.mapCoordinates.length !== 0) {
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
			} else if (this.mapSuggestion.length !== 0) {
				this.map.getView().setCenter(fromLonLat(this.mapSuggestion));
				this.map.getView().setZoom(this.mapZoom);
			}

			this.map.on('click', (evt) => {
				if (this.disable) {
					return;
				}

				this.selectedPoint = evt.coordinate;

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

				this.$emit('selected', {'coordinates': this.toLatLon(toLonLat(evt.coordinate)), 'map_zoom': this.map.getView().getZoom()})
			});

			this.filter = new Colorize();
			layer.addFilter(this.filter);

			// https://viglino.github.io/ol-ext/examples/filter/map.filter.colorize.html
			this.filter.setFilter({operation: 'difference', red:215, green: 255, blue: 255, value: 1});
			this.filter.setActive(this.$root.theme !== 'light-mode')
		},

		methods: {
			toLonLat: function(input) {
				if (input.hasOwnProperty("longitude") && input.hasOwnProperty("latitude")) {
					return [input["longitude"], input["latitude"]];
				} else {
					return [];
				}
			},
			toLatLon: function(input) {
				return {
					"latitude": input[1],
					"longitude": input[0],
				};
			},
		},

		watch: {
			coordinates: function(value) {
				this.mapCoordinates = this.toLonLat(value);
			},
			suggestion: function(value) {
				this.mapSuggestion = this.toLonLat(value);
			},
			disable: function(value) {
				for (let interaction of this.interactions.getArray()) {
					interaction.setActive(!value);
				}

				if (value) {
					this.map.getView().setCenter(this.selectedPoint);

					for (let control of this.controls.getArray()) {
						if (control instanceof Zoom) {
							this.map.removeControl(control);
						}
					}
				} else {
					this.map.addControl(new Zoom());
				}
			},
			'$root.theme': function(value) {
				this.filter.setActive(value !== 'light-mode')
			}
		}
	}
</script>
