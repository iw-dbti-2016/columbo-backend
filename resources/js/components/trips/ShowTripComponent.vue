<template>
	<div class="m-auto max-w-4xl my-8 py-10 rounded-lg w-full relative">
		<a @click.prevent="$router.go(-1)" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" href="#"><font-awesome-icon :icon="['fas', 'times']" /></a>
		<div class="flex flex-row justify-between">
			<div class="flex-grow pr-8 w-2/3">
				<h1 class="text-6xl tracking-wide uppercase">USA 2020</h1> <!-- NAME -->
				<p class="ml-2 text-gray-700 text-sm">Roadtrip west-coast USA summer '20.</p> <!-- SYNOPSIS -->
				<span class="block ml-2 mt-1 text-gray-700 text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span> <!-- OWNER -->
				<span class="block ml-2 mt-3 text-gray-700 text-lg">30/06/2020 - 2/08/2020</span> <!-- START AND END DATE -->
				<p class="leading-normal ml-2 mt-3 text-justify text-md">
					A roadtrip around the west-coast of the USA taking place in the summer of 2020.
					We will visit following states: CA, NV, (AZ), UT, ID, OR.
				</p> <!-- DESCRIPTION -->
			</div>
			<div class="mt-12 bg-gray-100 px-6 py-4 rounded-lg shadow-md w-1/3">
				<span class="block text-xl">Members</span> <!-- MEMBERS -->
				<ul class="text-gray-700 text-sm">
					<li class="mt-2"><a class="hover:underline" href="#">Stan Kelchtermans</a></li>
					<li class="mt-1"><a class="hover:underline" href="#">Maikel Both</a></li>
					<li class="mt-1"><a class="hover:underline" href="#">Devin Pelckmans</a></li>
					<li class="mt-1"><a class="hover:underline" href="#">Vik Vanderlinden</a></li>
				</ul>
				<span class="block mt-3 text-xl">Visitors</span> <!-- VISITORS -->
				<span class="block mt-2 text-gray-700 text-sm">No visitors on this trip.</span>
			</div>
		</div>
		<div class="mt-8 flex flex-row justify-between">
			<div class="flex-grow w-1/2"> <!-- REPORTS -->
				<span class="block text-2xl">Reports</span>
				<span class="block mt-2 text-gray-700">No reports written yet.</span>
			</div>
			<div class="flex-grow w-1/2"> <!-- PLANS -->
				<span class="block text-2xl">Planning</span>
				<span class="block mt-2 text-gray-700">No planning determined yet.</span>
			</div>
		</div>
	</div>
</template>

<script>
	export default {

	}
</script>
