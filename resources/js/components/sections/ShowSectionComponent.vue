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
		<div class="bg-primary">
			<div class="border-b-2 border-box-fade last:border-b-0 pb-4 relative">
				<div v-if="section.is_draft" class="w-full flex justify-around"><span class="px-4 py-2 text-white bg-green-500 rounded-full">DRAFT</span></div>
				<RichTextOutput v-bind:content="section.content" :locationables="section.locationables"></RichTextOutput>
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
	import LocationableOutput from 'Vue/components/locationables/LocationableOutput'
	import NProgress from 'nprogress'
	import Swal from 'sweetalert2'

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
            LocationableOutput,
        },

        data() {
            return {
                showMap: true,

                error: "",
            };
        },

        methods: {
			removeSection: function() {
				let toRemoveId = this.section.id;

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

								this.$emit('removed', toRemoveId);
							})
							.catch(this.handleError)
					}
				});
            },
            handleError(error) {
            	NProgress.done();

                if (error.response.status == 401) {
                	// log out
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
