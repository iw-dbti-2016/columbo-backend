<template>
	<div>
		<ActionBarComponent
				:title="section.start_time + ' - ' + section.end_time"
				:showEdit="true"
				v-on:edit="$emit('editing')"
				:showRemove="true"
				v-on:remove="removeSection"
				class="px-24">
		</ActionBarComponent>
		<div @click.prevent="$emit('creating')" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white cursor-pointer">Add a section</div>
		<div class="bg-primary mt-2">
			<div class="border-b-2 border-box-fade last:border-b-0 pb-4 relative">
				<div v-if="section.is_draft" class="w-full flex justify-around"><span class="px-4 py-2 text-white bg-green-500 rounded-full">DRAFT</span></div>
				<ShowLocationable :locationable="section.locationable"></ShowLocationable>
				<RichTextOutput class="px-24" v-bind:content="section.content"></RichTextOutput>
				<div class="px-24">
					<span class ="text-fade-more text-sm uppercase" :title="section.published_at">{{ section.published_at }}</span>
					<span class="block mt-1 text-fade-more text-xs uppercase">by <router-link :to="{name: 'showProfile', params: {username: section.owner.username}}" class="cursor-pointer hover:underline text-blue-600">{{ section.owner.first_name }} {{ section.owner.middle_name }} {{ section.owner.last_name }}</router-link></span>
				</div>
			</div>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
    import RichTextOutput from 'Vue/components/editor/RichTextOutput'
	import ShowLocationable from 'Vue/components/locationables/ShowLocationable'

    export default {
    	name: 'show-section',

		props: {
			section: {
				type: Object,
				default: {},
			},
		},

        components: {
            RichTextOutput,
            ShowLocationable,
        },

        data() {
            return {
                showMap: true,

                error: "",
            };
        },

        methods: {
            removeSection: function() {
                let tripId = this.$route.params.tripId;
                let reportId = this.$route.params.reportId;

                axios.delete(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${this.$route.params.sectionId}`)
                    .then((response) => {
                        this.$emit('removed', this.section);
                    })
                    .catch(this.handleError)
            },
            handleError(error) {
                if (error.response.status == 401) {
                    document.getElementById('logout').submit();
                    return;
                }

                this.error = error.response.data;
            },
            noResponse() {
                this.error = "No response...";
            },
        },
    }
</script>
