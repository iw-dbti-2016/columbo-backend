<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:backLink="{name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}"
				:editLink="{name: 'editSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: $route.params.sectionId}}"
				:showRemoveLink="true"
				v-on:removeclick="removeSection"
				title="Read more">
		</ActionBarComponent>
        <div class="flex flex-row justify-between">
		<div class="bg-primary mt-2">
			<div class="border-b-2 border-box-fade last:border-b-0 pb-4 relative">
				<div v-if="section.is_draft" class="w-full flex justify-around"><span class="px-4 py-2 text-white bg-green-500 rounded-full">DRAFT</span></div>
                <p class="leading-normal ml-2 mt-2 text-justify text-md">
				<RichTextOutput class="px-24" v-bind:content="section.content"></RichTextOutput>
                </p> <!-- DESCRIPTION -->
					<span class ="text-fade-more text-sm uppercase" :title="section.published_at">{{ section.published_at }}</span>
					<span class="block mt-1 text-fade-more text-xs uppercase">by <router-link :to="{name: 'showProfile', params: {username: section.owner.username}}" class="cursor-pointer hover:underline text-blue-600">{{ section.owner.first_name }} {{ section.owner.middle_name }} {{ section.owner.last_name }}</router-link></span>
				</div>
			</div>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import NProgress from 'nprogress'
    import SectionSideCardComponent from './SectionSideCardComponent.vue';
    import RichTextOutput from 'Vue/components/editor/RichTextOutput'

    export default {
    	name: 'show-section',

        components: {
            SectionSideCardComponent,
            RichTextOutput,
        },

        data() {
            return {
                section: {},
                showMap: true,

            	ready: false,
                error: "",
            };
        },

        beforeRouteEnter(to, from, next) {
            next(component => {
            	let tripId = component.$route.params.tripId;
            	let reportId = component.$route.params.reportId;
            	let sectionId = component.$route.params.sectionId;

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${sectionId}`)
                    .then(response => {
                    	component.section = response.data;
                    	component.ready = true;

                        NProgress.done()
                    })
                    .catch(component.handleError)
            })
        },

        methods: {
            removeSection: function() {
                let tripId = this.$route.params.tripId;
                let reportId = this.$route.params.reportId;

                axios.delete(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${this.$route.params.sectionId}`)
                    .then((response) => {
                        this.$router.push({name: 'showReport', params: {tripId: tripId, reportId: reportId}});
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
