<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
		<router-link :to="{name: 'home'}" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" title="Cancel"><font-awesome-icon :icon="['fas', 'times']" /></router-link>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3 relative">
				<h1 class="text-4xl tracking-wide">Create a new trip</h1>

				<div class="w-full mt-4">
					<div>
						<label class="text-gray-700 mt-3 block" for="name">Name</label>
						<input v-model="name" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="text">
						<div>
							<span></span>
							<span></span>
						</div>
					</div>
					<div>
						<label class="text-gray-700 mt-3 block" for="">Synopsis</label>
						<input v-model="synopsis" name="synopsis" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="text">
						<div>
							<span></span>
							<span></span>
						</div>
					</div>
					<div class="w-full flex flex-row justify-between">
						<div class="flex-grow w-1/2 mr-4">
							<label class="text-gray-700 mt-3 block" for="">Start date</label>
							<input v-model="startDate" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="date">
							<div>
								<span></span>
								<span></span>
							</div>
						</div>
						<div class="flex-grow w-1/2">
							<label class="text-gray-700 mt-3 block" for="">End date</label>
							<input v-model="endDate" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" type="date">
							<div>
								<span></span>
								<span></span>
							</div>
						</div>
					</div>
					<div>
						<label class="text-gray-700 mt-3 block" for="">Description</label>
						<textarea v-model="description" class="w-full block mt-2 px-4 py-3 bg-gray-100 shadow rounded focus:outline-none focus:shadow-md" name="" id="" cols="30" rows="10"></textarea>
						<div>
							<span></span>
							<span></span>
						</div>
					</div>
					<input @click.prevent="submitTrip" class="inline-block mt-4 px-4 py-3 bg-green-500 rounded text-white cursor-pointer focus:outline-none hover:bg-green-600 focus:bg-green-600 focus:shadow-lg" type="submit" value="Create this trip!">
					<router-link :to="{name: 'home'}" class="inline-block absolute right-0 mr-8 mt-4 px-4 py-3 bg-gray-100 rounded shadow focus:outline-none hover:bg-gray-200 focus:bg-gray-200 focus:shadow-md">Cancel</router-link>
				</div>
			</div>
			<div class="mt-12 w-1/3">
				<div class="px-6 py-4 rounded-lg shadow-md bg-gray-100">
					<span class="block text-xl">Members</span>
					<ul class="text-gray-700 text-sm">
						<li class="mt-2">No members yet</li>
						<li class="mt-1 text-blue-600"><a class="hover:underline" href="#">Add some friends</a></li>
					</ul>
					<span class="block mt-3 text-xl">Visitors</span>
					<ul class="text-gray-700 text-sm">
						<li class="mt-2">No visitors on this trip.</li>
						<li class="mt-1 text-blue-600"><a class="hover:underline" href="#">Add visitors</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
            return {
                name: "",
                synopsis: "",
                startDate: "",
                endDate: "",
                description: "",
            };
        },
        methods: {
            submitTrip: function() {
                axios.post('/api/v1/trips/create', {
                	name: this.name,
                	synopsis: this.synopsis,
                	start_date: this.startDate,
                	end_date: this.endDate,
                	description: this.description,
                	visibility: "friends", // TODO
                	//published_at for postponed publication
                })
                    .then((response) => {
                        console.log(response);
                        this.$store.commit('addTrip', response.data);
                        this.$router.push({name: 'showTrip', params: {tripId: response.data.data.id}});
                    })
                    .catch((error) => {
                        console.log("error: " + error);
                        console.log(error.response.data);
                    });
            },
        },
	}
</script>
