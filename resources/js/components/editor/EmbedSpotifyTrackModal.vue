<template>
	<div class="modal fixed top-0 left-0 bg-primary w-full h-screen z-50 flex items-center justify-center" v-if="show">
		<div class="max-w-xl w-full relative">
			<input class="bg-box text-primary focus:outline-none text-lg rounded px-4 py-4 w-full" placeholder="Spotify link or uri" v-model="url"/>
			<button class="text-gray-100 w-full mt-2 bg-green-700 h-12 rounded" @click="insertTrack">Embed Spotify track</button>
			<font-awesome-icon class="absolute top-0 right-0 -mr-8 -mt-8 text-4xl text-fade-more cursor-pointer" :icon="['fas', 'times']" @click="show=false" title="Cancel"></font-awesome-icon>
		</div>
	</div>
</template>

<script>
export default {
	name: 'embed-spotify-track-modal',

	data() {
		return {
			url: "",
			command: null,
			show: false
		};
	},

	computed: {
		trackID() {
			return this.spotifyParser(this.url);
		}
	},

	methods: {
		spotifyParser(url) {
			// FORMATS:
			// 		https://open.spotify.com/track/6txWz9UapYHVxEd7dDIHXT?si=mGjE79C5RTm3yykoTeuakA
			// 		spotify:track:6txWz9UapYHVxEd7dDIHXT
			const regExp = /^(spotify:track:|https:\/\/open\.spotify\.com\/track\/)([a-zA-Z0-9]+)(.*)$/;
			const match = url.match(regExp);
			return match && match[2].length === 22 ? match[2] : false;
		},
		showModal(command) {
			this.command = command;
			this.show = true;
		},
		insertTrack() {
			if (this.trackID === false) {
				return;
			}

			const data = {
				command: this.command,
				data: {
					src: `https://open.spotify.com/embed/track/${this.trackID}`
				}
			};

			this.$emit("onConfirm", data);
			this.show = false;
			this.url = "";
		}
	}
};
</script>
