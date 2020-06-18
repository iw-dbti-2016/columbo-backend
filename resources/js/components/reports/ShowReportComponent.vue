<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:showBack="true"
				v-on:back="$router.push({name: 'showTrip', params: {tripId: $route.params.tripId}})"
				:title="formatDate(report.date) + ': ' + report.title"
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
			<span class="block ml-2 mt-4 text-2xl text-primary">{{ humanTimeDiff(report.published_at) }}</span>
			<span class="block ml-2 mt-4 text-2xl text-primary">{{ report.visibility }}</span>
			<RichTextOutput v-bind:content="report.description"></RichTextOutput>
		</div>
		<div class="my-8 flex justify-between">
			<div class="-ml-8 max-h-screen mt-0 py-2 sticky top-0 w-24">
				<a v-if="activeSection > 0 && !editing && !creating" @click.prevent="previousSection"
						class="cursor-pointer flex focus:outline-none focus:text-fade h-full hover:bg-box-fade hover:text-fade items-center justify-center mr-4 rounded-r-lg text-fade-more w-full"
						title="Previous report">
					<font-awesome-icon class="text-2xl" :icon="['fas', 'arrow-left']" />
				</a>
			</div>
			<div class="mx-auto w-full max-w-5xl px-4 mt-1" v-if="sections.length > 0"> <!-- SECTIONS -->
				<CreateSectionComponent
						v-if="creating"
						v-on:back="creating = false"
						v-on:created="createSection">
				</CreateSectionComponent>
				<EditSectionComponent
						v-else-if="editing"
						v-model="sections[activeSection]"
						v-on:back="editing = false"
						v-on:updated="updateSection">
				</EditSectionComponent>
				<ShowSectionComponent
						v-else
						:section="sections[activeSection]"
						v-on:creating="creating = true"
						v-on:editing="editing = true"
						v-on:removed="removeSection">
				</ShowSectionComponent>
			</div>
			<span class="block mt-2 text-fade-more" v-else>No sections written yet.</span>
			<div class="-mr-4 max-h-screen mt-0 py-2 sticky top-0 w-24">
				<a v-if="activeSection < sections.length - 1 && !editing && !creating" @click.prevent="nextSection"
						class="cursor-pointer flex focus:outline-none focus:text-fade h-full hover:bg-box-fade hover:text-fade items-center justify-center ml-4 rounded-l-lg text-fade-more w-full"
						title="Next report">
					<font-awesome-icon class="text-2xl" :icon="['fas', 'arrow-right']" />
				</a>
			</div>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import NProgress from 'nprogress'
    import RichTextOutput from 'Vue/components/editor/RichTextOutput'
    import CreateSectionComponent from 'Vue/components/sections/CreateSectionComponent'
    import EditSectionComponent from 'Vue/components/sections/EditSectionComponent'
    import ShowSectionComponent from 'Vue/components/sections/ShowSectionComponent'

	export default {
		name: 'show-report',

		components: {
            RichTextOutput,
            CreateSectionComponent,
            EditSectionComponent,
            ShowSectionComponent,
        },

        data() {
            return {
                report: {},
                sections: [],
                activeSection: 0,
                editing: false,
                creating: false,

                ready: false,
                error: "",
            };
        },

        created() {
        	window.addEventListener('keyup', (e) => {
        		if (this.editing || this.creating) {
        			return;
        		}

        		if (e.key === "ArrowLeft") {
        			this.previousSection();
        		} else if (e.key === "ArrowRight") {
        			this.nextSection();
        		}
        	});
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

						if (window.location.hash !== "") {
							for (var i = component.sections.length - 1; i >= 0; i--) {
								if ("#" + component.sections[i].id === window.location.hash) {
									component.activeSection = i;
									break;
								}
							}
						}

                        NProgress.done()
                    })
                    .catch(component.handleError)
            })
        },

        methods: {
			nextSection: function() {
				if (this.activeSection < this.sections.length - 1) {
					this.activeSection++;
					window.location.hash = this.sections[this.activeSection].id;
					NProgress.done();
				}
			},
			previousSection: function() {
				if (this.activeSection > 0) {
					this.activeSection--;
					window.location.hash = this.sections[this.activeSection].id;
					NProgress.done();
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
			createSection: function(e) {
				this.sections.push(e);
				this.sortSectionsByStartTime();
				this.creating = false;

				this.activeSection = this.sections.findIndex(x => x.id === e.id);
				window.location.hash = e.id;
				NProgress.done();
			},
			updateSection: function(e) {
				// Section already updated in object by v-model
				this.editing = false;

				alert('updated');
				NProgress.done();
			},
			removeSection: function(removedId) {
				this.previousSection();

				this.sections = this.sections.filter(section => section.id !== removedId);

				NProgress.done();
			},
			handleError: function(error) {
				if (error.response.status == 401) {
					document.getElementById('logout').submit();
				}

				this.error = error.response.data;
			},
			sortSectionsByStartTime: function() {
				this.sections = this.sections.sort(function(a, b) {
					let timeA = a.start_time.split(':');
					let timeB = b.start_time.split(':');

					if (timeA[0] !== timeB[0]) return (timeA[0] < timeB[0]) ? -1 : 1;
					else return (timeA[1] < timeB[1]) ? -1 : 1;
				});
			}
        },
    }
</script>
