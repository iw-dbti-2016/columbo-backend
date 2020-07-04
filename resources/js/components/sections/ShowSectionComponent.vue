<template>
	<div>
		<div class="bg-primary">
			<div class="border-b-2 border-box-fade last:border-b-0 pb-4 relative">
				<ShowImage v-if="section.image" :image="section.image" :caption="section.image_caption" class="mt-6 mb-10"></ShowImage>
				<div class="px-24">
					<div class="relative bg-box rounded-lg shadow-md px-4 py-4 mt-3 flex items-center justify-between">
						<div class="text-primary text-4xl">{{ section.start_time }} - {{ section.end_time }}</div>
						<div v-if="section.temperature != null" class="text-primary text-3xl flex items-center">
							<font-awesome-icon class="text-3xl text-fade-more" :icon="['fas', section.weather_icon]" />
							<div class="ml-2">{{ section.temperature }}°C</div>
						</div>
						<div class="absolute left-0 top-0 w-full flex justify-around">
							<span v-if="section.is_draft" class="px-4 py-2 text-white bg-green-500 rounded-b-lg">DRAFT</span>
						</div>
					</div>
				</div>
				<RichTextOutput v-bind:content="section.content" :locationables="section.locationables"></RichTextOutput>
				<div class="px-24 flex items-center justify-between">
					<span class="block text-fade-more uppercase">
						<router-link :to="{name: 'showProfile', params: {username: section.owner.username}}" class="cursor-pointer hover:underline text-blue-400 flex items-center">
							<img class="w-6 h-6 mx-auto rounded-full" :src="(section.owner.image == null) ? 'https://www.gravatar.com/avatar/' + section.owner.email_hash : section.owner.image" alt="">
							<span class="ml-2 text-md py-1">{{ section.owner.first_name }} {{ section.owner.middle_name }} {{ section.owner.last_name }}</span>
						</router-link>
					</span>
					<span class="text-fade-more text-md uppercase" v-if="section.published_at" :title="formatDateTime(section.published_at)">{{ humanTimeDiff(section.published_at) }}</span>
					<span class="text-fade-more text-md uppercase" v-else>Not yet published</span>
				</div>
				<div class="px-24 mt-4 text-fade-more text-sm">This section is shared with: {{ section.visibility }}, which means only the friends of the members and visitors can see this section.</div>
			</div>
		</div>
	</div>
</template>

<script>
    import RichTextOutput from 'Vue/components/editor/RichTextOutput'
	import ShowImage from 'Vue/components/sections/ShowImage'

    export default {
    	name: 'show-section',

		props: {
			section: {
				type: Object,
				default: function() {
					return {};
				},
			},
		},

        components: {
            RichTextOutput,
            ShowImage,
        },

        data() {
            return {
                showMap: true,
                showFullImage: false,
            };
        },

        methods: {

        },
    }
</script>
