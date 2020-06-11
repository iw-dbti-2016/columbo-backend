<template>
	<div class="fixed top-0 right-0 w-16 h-screen flex flex-col border-gray-800 border-l-2 align-middle justify-between">
		<div class="flex flex-col h-full">
			<router-link :to="{name: 'showProfile', params: {username: user.username}}" class="w-full mt-6" title="Your profile">
				<img class="w-10 h-10 mx-auto rounded-full" :src="(user.image == null) ? 'https://www.gravatar.com/avatar/' + user.email_hash : user.image" alt="">
			</router-link>
			<router-link :to="{name: 'home'}" class="w-full mt-4" title="Home">
				<div class="w-12 h-12 rounded-full bg-gray-900 hover:bg-gray-800 mx-auto flex text-gray-400 justify-center">
					<font-awesome-icon class="text-2xl h-12" :icon="['fas', 'home']"></font-awesome-icon>
				</div>
			</router-link>
			<router-link :to="{name: 'home'}" class="w-full mt-4" title="Saved locations">
				<div class="w-12 h-12 rounded-full bg-gray-900 hover:bg-gray-800 mx-auto flex text-gray-400 justify-center">
					<font-awesome-icon class="text-2xl h-12" :icon="['fas', 'map-marker-alt']"></font-awesome-icon>
				</div>
			</router-link>
			<router-link :to="{name: 'home'}" class="w-full mt-4" title="Saved drafts">
				<div class="w-12 h-12 rounded-full bg-gray-900 hover:bg-gray-800 mx-auto flex text-gray-400 justify-center">
					<font-awesome-icon class="text-2xl h-12" :icon="['fas', 'align-left']"></font-awesome-icon>
				</div>
			</router-link>
			<router-link :to="{name: 'home'}" class="w-full mt-4" title="Finances">
				<div class="w-12 h-12 rounded-full bg-gray-900 hover:bg-gray-800 mx-auto flex text-gray-400 justify-center">
					<font-awesome-icon class="text-2xl h-12" :icon="['fas', 'coins']"></font-awesome-icon>
				</div>
			</router-link>
		</div>
		<div class="flex flex-col align-middle justify-end h-full">
			<router-link :to="{name: 'home'}" class="w-full mb-6" title="Settings">
				<div class="w-12 h-12 rounded-full bg-gray-900 hover:bg-gray-800 mx-auto flex text-gray-400 justify-center">
					<font-awesome-icon class="text-2xl h-12" :icon="['fas', 'cog']"></font-awesome-icon>
				</div>
			</router-link>
		</div>
	</div>
</template>

<script>
	export default {
		name: 'sidebar',
		data() {
			return {
				user: this.$store.getters['auth/user']
			};
		},
	}
</script>
