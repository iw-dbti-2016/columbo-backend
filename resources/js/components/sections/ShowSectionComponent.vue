<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
		<a @click.prevent="$router.push('/app/reports/1')" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-4 mt-8 py-2 right-0 text-3xl text-gray-400 top-0" href="/app/reports/1" title="Close this section"><font-awesome-icon :icon="['fas', 'times']" /></a>
		<a @click.prevent="$router.push('/app/sections/1/edit')" class="absolute cursor-pointer focus:outline-none focus:text-gray-600 mr-12 mt-8 py-3 right-0 text-2xl text-gray-400 top-0" href="/app/sections/1/edit" title="Edit this section"><font-awesome-icon :icon="['fas', 'edit']" /></a>
		<div>
			<span class="block ml-2 mt-1 text-gray-700 text-xs tracking-wider uppercase">by <a class="hover:underline text-blue-600" href="#">Vik Vanderlinden</a></span>
			<span class="block ml-2 mt-4 text-2xl">45 seconds ago</span>
			<p class="leading-normal ml-2 mt-2 text-justify text-md">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Placeat facilis dolorem totam alias, tempora quos perferendis aperiam expedita provident eligendi. Eaque alias maxime sunt qui accusantium nesciunt expedita aliquam dolore ratione nulla possimus doloremque assumenda earum tenetur nostrum, perspiciatis necessitatibus quaerat enim dicta tempore doloribus officia molestias. Ipsam voluptate, architecto harum, ab veniam est error repellendus reprehenderit suscipit, nemo culpa nulla. Ipsum voluptas veniam iure amet ducimus ipsam et, consectetur deserunt deleniti temporibus optio inventore rerum quia. Consectetur, hic dignissimos.</p> <!-- DESCRIPTION -->
		</div>
		<div class="mt-8 flex flex-row justify-between">
			<div class="flex-grow mr-4 w-2/3">
				<span class="block text-2xl">Lake Mead</span>
				<!-- <a @click.prevent="$router.push('/app/reports/create')" class="bg-blue-600 inline-block mt-2 px-4 py-2 rounded text-white" href="/app/reports/create">Create a new report</a> -->
				<!-- <span class="block mt-2 text-gray-700">No reports written yet.</span> -->
				<div class="bg-gray-100 mt-2 rounded-lg">
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative">
						<span class="text-gray-500 text-sm uppercase">45 seconds ago</span>
						<a @click.prevent="$router.push('/app/sections/1')" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer" href="/app/sections/1">details</a>
						<p class="leading-snug mt-4 text-justify">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati facere distinctio explicabo aut similique quaerat dolorum, illo dolor fugiat rem iste cupiditate pariatur, tempore dicta adipisci asperiores dolore nobis. Debitis eveniet expedita quia suscipit dolores, corporis, harum placeat a, facilis, nobis dignissimos accusamus recusandae. Laborum non omnis, molestias iure totam eius, culpa dolorum sit odio eaque esse quidem, inventore eos similique autem illo architecto! Nulla, voluptatem, alias. Beatae, facere earum nihil, itaque, vero repudiandae architecto sit sequi vel animi deleniti.
						</p>
					</div>
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative">
						<span class="text-gray-500 text-sm uppercase">12 minutes ago</span>
						<a @click.prevent="$router.push('/app/sections/1')" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer" href="/app/sections/1">details</a>
						<p class="leading-snug mt-4 text-justify">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati facere distinctio explicabo aut similique quaerat dolorum, illo dolor fugiat rem iste cupiditate pariatur, tempore dicta adipisci asperiores dolore nobis. Debitis eveniet expedita quia suscipit dolores, corporis, harum placeat a, facilis, nobis dignissimos accusamus recusandae. Laborum non omnis, molestias iure totam eius, culpa dolorum sit odio eaque esse quidem, inventore eos similique autem illo architecto! Nulla, voluptatem, alias. Beatae, facere earum nihil, itaque, vero repudiandae architecto sit sequi vel animi deleniti.
						</p>
					</div>
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative">
						<span class="text-gray-500 text-sm uppercase">20 minutes ago</span>
						<a @click.prevent="$router.push('/app/sections/1')" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer" href="/app/sections/1">details</a>
						<p class="leading-snug mt-4 text-justify">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati facere distinctio explicabo aut similique quaerat dolorum, illo dolor fugiat rem iste cupiditate pariatur, tempore dicta adipisci asperiores dolore nobis. Debitis eveniet expedita quia suscipit dolores, corporis, harum placeat a, facilis, nobis dignissimos accusamus recusandae. Laborum non omnis, molestias iure totam eius, culpa dolorum sit odio eaque esse quidem, inventore eos similique autem illo architecto! Nulla, voluptatem, alias. Beatae, facere earum nihil, itaque, vero repudiandae architecto sit sequi vel animi deleniti.
						</p>
					</div>
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative">
						<span class="text-gray-500 text-sm uppercase">1 hour ago</span>
						<a @click.prevent="$router.push('/app/sections/1')" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer" href="/app/sections/1">details</a>
						<p class="leading-snug mt-4 text-justify">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati facere distinctio explicabo aut similique quaerat dolorum, illo dolor fugiat rem iste cupiditate pariatur, tempore dicta adipisci asperiores dolore nobis. Debitis eveniet expedita quia suscipit dolores, corporis, harum placeat a, facilis, nobis dignissimos accusamus recusandae. Laborum non omnis, molestias iure totam eius, culpa dolorum sit odio eaque esse quidem, inventore eos similique autem illo architecto! Nulla, voluptatem, alias. Beatae, facere earum nihil, itaque, vero repudiandae architecto sit sequi vel animi deleniti.
						</p>
					</div>
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative">
						<span class="text-gray-500 text-sm uppercase">2 hours ago</span>
						<a @click.prevent="$router.push('/app/sections/1')" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer" href="/app/sections/1">details</a>
						<p class="leading-snug mt-4 text-justify">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati facere distinctio explicabo aut similique quaerat dolorum, illo dolor fugiat rem iste cupiditate pariatur, tempore dicta adipisci asperiores dolore nobis. Debitis eveniet expedita quia suscipit dolores, corporis, harum placeat a, facilis, nobis dignissimos accusamus recusandae. Laborum non omnis, molestias iure totam eius, culpa dolorum sit odio eaque esse quidem, inventore eos similique autem illo architecto! Nulla, voluptatem, alias. Beatae, facere earum nihil, itaque, vero repudiandae architecto sit sequi vel animi deleniti.
						</p>
					</div>
					<div class="border-b-8 border-white last:border-0 px-5 py-4 relative">
						<span class="text-gray-500 text-sm uppercase">3 hours ago</span>
						<a @click.prevent="$router.push('/app/sections/1')" class="absolute capitalize hover:underline mr-5 mt-4 right-0 text-blue-600 text-sm top-0 cursor-pointer" href="/app/sections/1">details</a>
						<p class="leading-snug mt-4 text-justify">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati facere distinctio explicabo aut similique quaerat dolorum, illo dolor fugiat rem iste cupiditate pariatur, tempore dicta adipisci asperiores dolore nobis. Debitis eveniet expedita quia suscipit dolores, corporis, harum placeat a, facilis, nobis dignissimos accusamus recusandae. Laborum non omnis, molestias iure totam eius, culpa dolorum sit odio eaque esse quidem, inventore eos similique autem illo architecto! Nulla, voluptatem, alias. Beatae, facere earum nihil, itaque, vero repudiandae architecto sit sequi vel animi deleniti.
						</p>
					</div>
				</div>
			</div>
			<div class="flex-grow w-1/3">
				<div class="sticky top-0 pt-8">
					<div class="bg-gray-100 rounded-lg shadow-lg overflow-hidden">
						<div class="px-6 py-4 relative">
							<span class="text-4xl">14:40</span>
							<span class="absolute top-0 right-0 mt-4 mr-5 text-4xl">3h40</span>
						</div>
						<div class="relative">
							<img src="/img/example-map.png" alt="#">
							<div class="absolute bg-gray-100 flex flex-col items-center mr-2 mt-2 px-1 px-2 py-3 right-0 rounded-full text-2xl text-xl top-0">
								<font-awesome-icon class="cursor-default" :icon="['fas', 'map-marker-alt']" />
								<font-awesome-icon class="mt-2 cursor-pointer text-gray-600 hover:text-black" :icon="['far', 'image']" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {

	}
</script>
