<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent
				:showBack="true"
				v-on:back="$router.push({name: 'home'})"
				:title="trip.name"
				:showToggleTheme="true"
				:showEdit="true"
				v-on:edit="$router.push({name: 'editTrip', params: {'tripId': $route.params.tripId}})"
				:showRemove="true"
				v-on:remove="removeTrip"
				class="mt-4">
		</ActionBarComponent>
		<ProgressBarComponent class="py-2 px-2 mt-3" :start="trip.start_date" :end="trip.end_date" :current="(new Date()).toISOString()"></ProgressBarComponent>
        <div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3">
				<p class="ml-2 text-fade text-sm">{{ trip.synopsis }}</p> <!-- SYNOPSIS -->
				<span class="block ml-2 mt-1 text-fade text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span> <!-- OWNER -->
				<p class="leading-normal ml-2 mt-3 text-justify text-md">
                    <RichTextOutput :content="trip.description"></RichTextOutput>
                </p> <!-- DESCRIPTION -->
			</div>
			<div class="mt-12 w-1/3">
				<div class="bg-box px-6 py-4 rounded-lg shadow-md">
					<span class="block text-xl text-primary">Members</span> <!-- MEMBERS -->
					<ul class="text-fade-more text-sm">
						<li class="mt-2"><a class="hover:underline" href="#">Stan Kelchtermans</a></li>
						<li class="mt-1"><a class="hover:underline" href="#">Maikel Both</a></li>
						<li class="mt-1"><a class="hover:underline" href="#">Devin Pelckmans</a></li>
						<li class="mt-1"><a class="hover:underline" href="#">Vik Vanderlinden</a></li>
					</ul>
					<span class="block mt-3 text-xl text-primary">Visitors</span> <!-- VISITORS -->
					<span class="block mt-2 text-fade-more text-sm">No visitors on this trip.</span>
				</div>
			</div>
		</div>
		<div class="mt-8 flex flex-row justify-between">
			<div class="flex-grow mr-4 w-1/2"> <!-- REPORTS -->
				<span class="block text-2xl text-primary">Reports</span>
				<router-link :to="{name: 'createReport', params: {tripId: $route.params.tripId}}" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white">Create a new report</router-link>
				<span v-if="reports.length == 0" class="block mt-2 text-fade">No reports written yet.</span>
				<div v-else class="bg-box mt-2 rounded-lg shadow-md">
					<div v-for="report in reports" @click.prevent="$router.push({name: 'showReport', params: {tripId: $route.params.tripId, reportId: report.id}})" class="border-b-2 border-primary last:border-b-0 px-5 py-4 text-md cursor-pointer text-fade">{{ formatDate(report.date) }} - {{ report.title }}</div>
				</div>
			</div>
			<div class="flex-grow w-1/2"> <!-- PLANS -->
				<span class="block text-2xl text-primary">Planning</span>
				<span class="block mt-2 text-fade-more">No planning determined yet.</span>
				<span class="block mt-2 text-fade-more">Planning is not supported yet!</span>
			</div>
		</div>
        <ErrorHandlerComponent :error.sync="error"></ErrorHandlerComponent>
	</div>
</template>

<script>
	import RichTextOutput from 'Vue/components/editor/RichTextOutput'

	export default {
		name: 'show-trip',

		components: {
			RichTextOutput
		},

        data() {
            return {
                trip: {},
                reports: [],

            	ready: false,
                error: "",
            };
        },

        beforeRouteEnter(to, from, next) {
            next(component => {
                axios.get(`/api/v1/trips/${component.$route.params.tripId}`)
                    .then(response => {
                    	component.trip = response.data;
                    	component.reports = response.data.reports;
                    	component.ready = true;

                        component.stopLoading();
                    })
                    .catch(component.handleError)
            })
        },
        methods: {
            removeTrip: function() {
                let tripId = this.$route.params.tripId;

                axios.delete(`/api/v1/trips/${tripId}`)
                    .then((response) => {
                        this.$router.push({name: 'home'});
                    })
                    .catch(this.handleError);
            },
            handleError: function(error) {
                if (error.response.status == 401) {
                    document.getElementById('logout').submit();
                }

                this.userData = error.response.data;
            },
        },
    }
</script>
