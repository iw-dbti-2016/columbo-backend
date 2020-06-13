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
			<div class="flex-grow mr-8 w-2/3">
				<span class="block ml-2 mt-1 text-fade-more text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span>
				<span class="block ml-2 mt-4 text-2xl">{{ section.published_at_diff }}</span>
                <p class="leading-normal ml-2 mt-2 text-justify text-md">
                	<RichTextOutput v-bind:content="section.content"></RichTextOutput>
                </p> <!-- DESCRIPTION -->
			</div>
			<div class="flex-grow w-1/3">
                <SectionSideCardComponent :section="section"></SectionSideCardComponent>
			</div>
		</div>
		<div class="mt-8" v-if="section.locationable != null">
			<span class="block text-2xl text-primary">{{ section.locationable.location.name }}</span>
			<div class="bg-box mt-2 rounded-lg flex flex-row overflow-hidden shadow-md">
				<div class="flex-grow w-2/3 px-5 py-4 relative text-primary">{{ section.locationable.location.info }}</div>
				<div class="flex flex-col flex-grow items-center justify-around w-1/3">
					<img class="bg-box flex-grow-0 rounded-lg" src="http://via.placeholder.com/500x250" alt="#">
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
