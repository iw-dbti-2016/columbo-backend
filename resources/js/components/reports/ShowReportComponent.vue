<template>
	<div class="m-auto pl-8 pr-24 w-full">
		<ActionBarComponent
				:backLink="{name: 'showTrip', params: {tripId: $route.params.tripId}}"
				:editLink="{name: 'editReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}"
				:showRemoveLink="true"
				v-on:removeclick="removeReport"
				:title="report.title">
		</ActionBarComponent>
		<div>
			<span class="block ml-2 mt-1 text-gray-700 text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span> <!-- OWNER -->
			<span class="block ml-2 mt-4 text-2xl">{{ report.date }}</span> <!-- DATE -->
            <MarkdownOutputComponent v-bind:content="report.description"></MarkdownOutputComponent>
		</div>
		<div class="mt-8 flex flex-row justify-between">
			<div class="flex-grow mr-4 w-2/3"> <!-- SECTIONS -->
				<span class="block text-2xl">Report</span>
				<!-- <div class="-ml-20 h-0 rotate-270 sticky text-6xl text-right top-0 uppercase">
					<span class="pr-8">{{ report.title }}</span>
				</div> -->
				<router-link :to="{name: 'createSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white">Add a section</router-link>
				<div class="bg-gray-100 mt-2 rounded-lg" v-if="sections.length > 0">
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative" @mouseover="mouseOverSection(index)" :key="section.id" v-for="(section, index) in sections">
						<span class="text-gray-500 text-sm uppercase" :title="section.published_at">{{ section.published_at_diff }}</span>
						<span class="block mt-1 text-gray-500 text-xs uppercase">by <router-link :to="{name: 'showProfile', params: {username: section.owner.username}}" class="cursor-pointer hover:underline text-blue-600">{{ section.owner.first_name }} {{ section.owner.middle_name }} {{ section.owner.last_name }}</router-link></span>
						<router-link :to="{name: 'showSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: section.id}}" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer">details</router-link>
                        <MarkdownOutputComponent v-bind:content="section.content"></MarkdownOutputComponent>
					</div>
				</div>
				<span class="block mt-2 text-gray-700" v-else-if="loading">Loading sections.</span>
				<span class="block mt-2 text-gray-700" v-else>No sections written yet.</span>
			</div>
			<div class="flex-grow w-1/3">
				<div class="sticky top-0 pt-8">
					<SectionSideCardComponent :section="sections[sectionInfoIndex]" :loading="loading"></SectionSideCardComponent>
				</div>
			</div>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
    import SectionSideCardComponent from './../sections/SectionSideCardComponent.vue';

	export default {
		components: {
            SectionSideCardComponent,
        },
        data() {
            return {
                report: {},
                sections: [],
                sectionInfoIndex: 0,

                loading: true,
                error: "",
            };
        },
        created() {
        	this.getReport();
        	this.getSections();
        },
        methods: {
            getReport: function() {
            	let tripId = this.$route.params.tripId;
            	let reportId = this.$route.params.reportId;

            	// if (this.$store.getters.hasReportWithId(reportId)) {
            	// 	this.report = this.$store.getters.getReportById(reportId)[0];
            	// 	return;
            	// }

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}`)
                    .then((response) => {
                    	// this.$store.commit('addReport', response.data);
                        this.report = response.data;
                    })
                    .catch(this.handleError);
            },
            getSections: function() {
            	axios.get(`/api/v1/trips/${this.$route.params.tripId}/reports/${this.$route.params.reportId}/sections`)
                    .then((response) => {
                        this.sections = response.data;
                        // this.$store.commit('setSections', response.data);
                    })
                    .catch(this.handleError)
                    .finally(this.stopLoading);
            },
            mouseOverSection: function(index) {
            	if (this.sectionInfoIndex != index) {
            		this.sectionInfoIndex = index;
            	}
            },
            removeReport: function() {
                let tripId = this.$route.params.tripId;

                axios.delete(`/api/v1/trips/${tripId}/reports/${this.$route.params.reportId}`)
                    .then((response) => {
                        this.$router.push({name: 'showTrip', params: {tripId: tripId}});
                    })
                    .catch(this.handleError);
            },
            handleError: function(error) {
                if (error.response.status == 401) {
                    document.getElementById('logout').submit();
                }

                this.error = error.response.data;
            },
            stopLoading: function() {
                this.loading = false;
            }
        },
    }
</script>
