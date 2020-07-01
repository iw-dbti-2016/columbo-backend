<template>
	<div class="m-auto pr-16 w-full" v-if="ready">
		<ActionBarComponent
				:showBack="true"
				v-on:back="$router.push({name: 'showTrip', params: {tripId: $route.params.tripId}})"
				:title="formatDate(report.date)"
				:showToggleTheme="true"
				:showEdit="true"
				v-on:edit="$router.push({name: 'editReport', params: {tripId: $route.params.tripId, reportId: $route.params.reportId}})"
				:showRemove="true"
				v-on:remove="removeReport"
				:showExtraAction="true"
				:extraActionTitle="report.is_locked ? 'Re-open this report' : 'Lock this report'"
				:extraActionIcon="report.is_locked ? 'lock' : 'lock-open'"
				v-on:extraaction="toggleLock"
				class="mt-4 px-8">
		</ActionBarComponent>
		<div class="mt-5 max-w-5xl mx-auto px-4">
			<div class="px-24">
				<div class="bg-box" v-if="report.hasOwnProperty('plan')">
					program: Some text descriping the planning<br>
					driving distance: 35 km<br>
					wifi available: false<br>
					sleeping location: Alpha Campsite, Beta<br>
					estimated price: 82.20<br>
					currency: USD<br>
					status sleep: TODO/IN PROGRESS<br>
					status activities: TO CONFIRM/DONE<br>
					visibility: friends<br>
					published at: 24/04/2021<br>
				</div>
				<div class="text-primary text-5xl">{{ report.title}}</div>
				<!-- <div class="text-primary text-xl mt-2">{{ formatDate(report.date) }}</div> -->
				<!-- <span class="block mt-4 text-md text-fade-more">Shared with: {{ report.visibility }}</span> -->
				<!-- <div class="mt-2 flex items-center justify-between">
					<span class="block text-fade-more uppercase">
						<router-link :to="{name: 'showProfile', params: {username: report.owner.username}}" class="cursor-pointer hover:underline text-blue-400 flex items-center">
							<img class="w-6 h-6 mx-auto rounded-full" :src="(report.owner.image == null) ? 'https://www.gravatar.com/avatar/' + report.owner.email_hash : report.owner.image" alt="">
							<span class="ml-2 text-md py-1">{{ report.owner.first_name }} {{ report.owner.middle_name }} {{ report.owner.last_name }}</span>
						</router-link>
					</span>
					<span class="block ml-2 mt-4 text-fade-more text-md uppercase">{{ humanTimeDiff(report.published_at) }}</span>
				</div> -->
			</div>
			<RichTextOutput v-bind:content="report.description"></RichTextOutput>
			<div class="px-24 relative">
				<div class="flex items-center h-12">
					<a @click.prevent="() => {creating = true;editing=false;}" v-if="!creating && !editing"
							class="flex-shrink-0 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
							title="Create a new section">
						<font-awesome-icon class="text-2xl" :icon="['fas', 'plus']" />
					</a>
					<hr class="border-box w-full border-b-2">
					<a @click.prevent="editing = true" v-if="!creating && !editing"
							class="flex-shrink-0 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
							title="Edit this section">
						<font-awesome-icon class="text-2xl" :icon="['fas', 'pen']" />
					</a>
					<hr class="border-box border-b-2 w-2">
					<a @click.prevent="removeSection" v-if="!creating && !editing"
							class="flex-shrink-0 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade bg-box w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
							title="Remove this section">
						<font-awesome-icon class="text-2xl" :icon="['fas', 'trash-alt']" />
					</a>
				</div>
			</div>
		</div>
		<div class="flex justify-between" id="sections-top">
			<div class="max-h-screen mt-0 py-2 sticky top-0 w-24">
				<a v-if="activeSection > 0 && !editing && !creating" @click.prevent="previousSection"
						class="cursor-pointer flex focus:outline-none focus:text-fade h-full hover:bg-box-fade hover:text-fade items-center justify-center rounded-r-lg text-fade-more w-full"
						title="Previous report">
					<font-awesome-icon class="text-2xl" :icon="['fas', 'arrow-left']" />
				</a>
			</div>
			<div class="mb-8 mx-auto w-full min-h-screen max-w-5xl px-4" v-if="sections.length > 0 || creating"> <!-- SECTIONS -->
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
						class="mt-8">
				</ShowSectionComponent>
			</div>
			<span class="block mt-2 text-fade-more" v-else>No sections written yet.</span>
			<div class="max-h-screen mt-0 py-2 sticky top-0 w-24">
				<a v-if="activeSection < sections.length - 1 && !editing && !creating" @click.prevent="nextSection"
						class="cursor-pointer flex focus:outline-none focus:text-fade h-full hover:bg-box-fade hover:text-fade items-center justify-center rounded-l-lg text-fade-more w-full"
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
	import Swal from 'sweetalert2'

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
        	window.addEventListener('keyup', this.onKeyUp);
        },

        destroyed() {
        	window.removeEventListener('keyup', this.onKeyUp);
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
        	toggleLock: function() {
        		console.error('Lock/Unlock not implemented');
        		alert('Lock/Unlock not implemented');
        	},
        	onKeyUp: function(e) {
				if (this.editing || this.creating) {
					return;
				}

				if (e.key === "ArrowLeft") {
					this.previousSection();
				} else if (e.key === "ArrowRight") {
					this.nextSection();
				}
        	},
			nextSection: function() {
				if (this.activeSection < this.sections.length - 1) {
					this.activeSection++;
					window.location.hash = this.sections[this.activeSection].id;
					document.getElementById('sections-top').scrollIntoView();
					NProgress.done();
				}
			},
			previousSection: function() {
				if (this.activeSection > 0) {
					this.activeSection--;
					window.location.hash = this.sections[this.activeSection].id;
					document.getElementById('sections-top').scrollIntoView();
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
				NProgress.done();
			},
			removeSection: function() {
				let toRemoveId = this.sections[this.activeSection].id;

				Swal.fire({
					title: "Are you sure?",
					text: "Once deleted, you will not be able to recover this section!",
					icon: "warning",
					showCancelButton: true,
					confirmButtonText: 'Yes, delete it!',
					customClass: {
						confirmButton: "green-button",
						cancelButton: "red-button",
					},
					target: document.getElementById('parent-element'),
				})
				.then((result) => {
					if (result.value) {

						NProgress.start();

						let tripId = this.$route.params.tripId;
						let reportId = this.$route.params.reportId;

						axios.delete(`/api/v1/trips/${tripId}/reports/${reportId}/sections/${toRemoveId}`)
							.then((response) => {
								Swal.fire({
									title: "This section has been deleted!",
									icon: "success",
									target: document.getElementById('parent-element'),
								});

								this.filterSection(toRemoveId);
							})
							.catch(this.handleError)
					}
				});
			},
			filterSection: function(removedId) {
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
