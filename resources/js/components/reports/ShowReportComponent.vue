<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:showBack="true"
				v-on:back="$router.push({name: 'showTrip', params: {tripId: $route.params.tripId}})"
				:title="report.title"
				:showToggleTheme="true"
				:showEdit="true"
				v-on:edit="$router.push({name: 'editReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}})"
				:showRemove="true"
				v-on:remove="removeReport">
		</ActionBarComponent>
		<div>
			<div class="text-primary">PLAN</div>
			<div class="absolute right-0 mr-24 top-0 mt-24" :title="`This report is ${report.is_locked ? 'locked' : 'open'}`">
				<font-awesome-icon class="text-xl text-primary" v-if="report.is_locked" :icon="['fas', 'lock']"/>
				<font-awesome-icon class="text-xl text-primary" v-else :icon="['fas', 'lock-open']"/>
			</div>
			<span class="block ml-2 mt-1 text-fade-more text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span> <!-- OWNER -->
			<span class="block ml-2 mt-4 text-2xl text-primary">{{ report.date }}</span> <!-- DATE -->
			<span class="block ml-2 mt-4 text-2xl text-primary">{{ report.published_at }}</span>
			<span class="block ml-2 mt-4 text-2xl text-primary">{{ report.visibility }}</span>
            <RichTextOutput v-bind:content="report.description"></RichTextOutput>
		</div>
		<div class="mt-8 flex flex-row justify-between">
			<div class="flex-grow mr-4 w-2/3"> <!-- SECTIONS -->
				<span class="block text-2xl text-primary">Report</span>
				<router-link :to="{name: 'createSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}}" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white">Add a section</router-link>
				<div class="bg-box mt-2 rounded-lg" v-if="sections.length > 0">
					<div class="border-b-8 border-primary last:border-0 px-5 py-4 relative" @mouseover="mouseOverSection(index)" :key="section.id" v-for="(section, index) in sections">
						<span class="text-fade-more text-sm uppercase" :title="section.published_at">{{ section.published_at_diff }}</span>
						<span class="block mt-1 text-fade-more text-xs uppercase">by <router-link :to="{name: 'showProfile', params: {username: section.owner.username}}" class="cursor-pointer hover:underline text-blue-600">{{ section.owner.first_name }} {{ section.owner.middle_name }} {{ section.owner.last_name }}</router-link></span>
						<router-link :to="{name: 'showSection', params: {tripId: $route.params.tripId, reportId: $route.params.reportId, sectionId: section.id}}" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer">details</router-link>
                        <RichTextOutput v-bind:content="section.content"></RichTextOutput>
					</div>
				</div>
				<span class="block mt-2 text-fade-more" v-else-if="!ready">Loading sections.</span>
				<span class="block mt-2 text-fade-more" v-else>No sections written yet.</span>
			</div>
			<div class="flex-grow w-1/3">
				<div class="sticky top-0 pt-8">
					<SectionSideCardComponent :section="sections[sectionInfoIndex]" :loading="!ready"></SectionSideCardComponent>
				</div>
			</div>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import NProgress from 'nprogress'
    import SectionSideCardComponent from './../sections/SectionSideCardComponent.vue';
    import RichTextOutput from 'Vue/components/editor/RichTextOutput'

	export default {
		name: 'show-report',

		components: {
            SectionSideCardComponent,
            RichTextOutput,
        },

        data() {
            return {
                report: {},
                sections: [],
                sectionInfoIndex: 0,

                ready: false,
                error: "",
            };
        },

        beforeRouteEnter(to, from, next) {
            next(component => {
            	let tripId = component.$route.params.tripId;
            	let reportId = component.$route.params.reportId;

                axios.get(`/api/v1/trips/${tripId}/reports/${reportId}`)
                    .then(response => {
                    	component.report = response.data;
                    	component.sections = response.data.sections;
                    	component.ready = true;

                        NProgress.done()
                    })
                    .catch(component.handleError)
            })
        },

        methods: {
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
        },
    }
</script>
