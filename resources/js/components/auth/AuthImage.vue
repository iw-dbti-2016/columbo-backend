<template>
	<div class="my-auto hidden lg:block w-1/2">
        <h1 class="text-2xl text-center">Go Forward<br><span class="text-blue-600 font-semibold">Don't look back!</span></h1>
        <img :src="source + '/' + image" :alt="alt">
    </div>
</template>

<script>
	export default {
		props: [
			'source', 'image', 'alt',
		]
	}
</script>
