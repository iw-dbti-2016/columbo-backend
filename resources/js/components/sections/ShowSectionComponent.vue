<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
		<router-link :to="{name: 'showReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" title="Close this section"><font-awesome-icon :icon="['fas', 'times']" /></router-link>
		<router-link :to="{name: 'editSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: $route.params.sectionId}}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-12 mt-8 py-3 right-0 text-2xl text-gray-400 top-0" title="Edit this section"><font-awesome-icon :icon="['fas', 'edit']" /></router-link>
		<div @click.prevent="removeSection" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-24 mt-8 py-3 right-0 text-2xl text-gray-400 top-0" title="Remove this section"><font-awesome-icon :icon="['fas', 'trash-alt']" /></div>
        <div class="flex flex-row justify-between">
			<div class="flex-grow mr-8 w-2/3">
				<span class="block ml-2 mt-1 text-gray-700 text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span>
				<span class="block ml-2 mt-4 text-2xl">{{ section.published_at_diff }}</span>
                <p class="leading-normal ml-2 mt-2 text-justify text-md"><MarkdownOutputComponent v-bind:content="section.content"></MarkdownOutputComponent></p> <!-- DESCRIPTION -->
			</div>
			<div class="flex-grow w-1/3">
                <SectionSideCardComponent :section="section"></SectionSideCardComponent>
			</div>
		</div>
		<div class="mt-8" v-if="section.locationable != null">
			<span class="block text-2xl">{{ section.locationable.name }}</span>
			<div class="bg-gray-100 mt-2 rounded-lg flex flex-row overflow-hidden shadow-md">
				<div class="flex-grow w-2/3 px-5 py-4 relative">{{ section.locationable.info }}</div>
				<div class="flex flex-col flex-grow items-center justify-around w-1/3">
					<img class="bg-white flex-grow-0 p-2 rounded" src="/img/example-map.png" alt="#">
				</div>
			</div>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
    import SectionSideCardComponent from './SectionSideCardComponent.vue';

    export default {
        components: {
            SectionSideCardComponent,
        },
        data() {
            return {
            	loading: true,
                section: {},
                showMap: true,
                error: "",
            };
        },
        created() {
        	this.getSection();
        },
        methods: {
            getSection: function() {
            	let tripId = this.$route.params.tripId;
            	let reportId = this.$route.params.reportId;
            	let sectionId = this.$route.params.sectionId;

            	if (this.$store.getters.hasSectionWithId(sectionId)) {
            		this.section = this.$store.getters.getSectionById(sectionId)[0];
            		this.loading = false;
            		return;
            	}

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${sectionId}`)
                    .then((response) => {
                    	this.$store.commit('addSection', response.data);
                        this.section = response.data.data;
                    })
                    .catch(this.handleError)
                    .finally(this.stopLoading);
            },
            removeSection: function() {
                let tripId = this.$route.params.tripId;
                let reportId = this.$route.params.reportId;

                axios.delete(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${this.$route.params.sectionId}`)
                    .then((response) => {
                        this.$router.push({name: 'showReport', params: {tripId: tripId, reportId: reportId}});
                    })
                    .catch(this.handleError)
                    .finally(this.stopLoading);
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
            stopLoading() {
                this.loading = false;
            },
        },
    }
</script>
