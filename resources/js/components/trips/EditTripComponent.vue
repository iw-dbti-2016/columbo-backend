<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:showBack="true"
				v-on:back="$router.push({name: 'showTrip', params: {'tripId': $route.params.tripId}})"
				title="Edit trip"
				:showToggleTheme="true"
				class="mt-4">
		</ActionBarComponent>
		<div class="flex flex-row justify-between mt-4">
			<div class="flex-grow pr-8 w-2/3 relative">
				<div class="w-full mt-4">
					<FormInput label="Name" v-model="trip.name"></FormInput>
					<FormInput label="Synopsis" v-model="trip.synopsis"></FormInput>
					<div class="w-full flex flex-row justify-between">
						<div class="flex-grow w-1/2 mr-4">
							<FormInput label="Start date" type="date" v-model="trip.start_date"></FormInput>
						</div>
						<div class="flex-grow w-1/2">
							<FormInput label="End date" type="date" v-model="trip.end_date"></FormInput>
						</div>
					</div>
					<RichTextInput label="Description" :content.sync="trip.description"></RichTextInput>
					<input @click.prevent="updateTrip" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Update this trip!">
					<router-link :to="{name: 'showTrip', params: {'tripId': $route.params.tripId}}" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 bg-box rounded shadow text-primary focus:outline-none hover:bg-box-fade focus:bg-box-fade focus:shadow-md">Cancel</router-link>
				</div>
			</div>
			<div class="mt-12 w-1/3">
				<div class="px-6 py-4 rounded-lg shadow-md bg-box">
					<span class="block text-xl text-primary">Members</span>
					<ul class="text-fade-more text-sm">
						<li class="mt-2">No members yet</li>
						<li class="mt-1 text-blue-600"><a class="hover:underline" href="#">Add some friends</a></li>
					</ul>
					<span class="block mt-3 text-xl text-primary">Visitors</span>
					<ul class="text-fade-more text-sm">
						<li class="mt-2">No visitors on this trip.</li>
						<li class="mt-1 text-blue-600"><a class="hover:underline" href="#">Add visitors</a></li>
					</ul>
				</div>
			</div>
		</div>
		<ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import RichTextInput from 'Vue/components/editor/RichTextInput'
	import FormInput from 'Vue/components/forms/FormInput'

	export default {
		name: 'edit-trip',

		components: {
			RichTextInput,
			FormInput,
		},

		data() {
            return {
                trip: {},
                ready: false,
                error: "",
            };
        },

        beforeRouteEnter(to, from, next) {
            next(component => {
                axios.get(`/api/v1/trips/${component.$route.params.tripId}`)
                    .then(response => {
                    	component.trip = response.data;
                    	component.ready = true;

                        component.stopLoading();
                    })
                    .catch(component.handleError)
            })
        },

        methods: {
            updateTrip: function() {
            	let tripId = this.$route.params.tripId;

                axios.patch(`/api/v1/trips/${tripId}`, {
                	name: this.trip.name,
                	synopsis: this.trip.synopsis,
                	start_date: this.trip.start_date,
                	end_date: this.trip.end_date,
                	description: this.trip.description,
                	visibility: "friends", // TODO
                	//published_at for postponed publication
                })
                    .then((response) => {
                        this.$router.push({name: 'showTrip', params: {tripId: tripId}});
                    })
                    .catch((e) => this.handleError(e));
            },
        },
	}
</script>
