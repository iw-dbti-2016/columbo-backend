<template>
	<div class="bg-primary page antialiased leading-none font-sans pt-1 min-h-screen" :class="this.$root.theme" id="parent-element">
		<SideBarComponent v-if="this.$router.currentRoute.meta.requiresAuthentication"></SideBarComponent>
		<transition name="component-fade" mode="out-in">
			<router-view></router-view>
		</transition>
	</div>
</template>

<script>
	import SideBarComponent from './components/global/SideBarComponent.vue'

	export default {
		components: {
			SideBarComponent
		},
	}
</script>
