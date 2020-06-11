<template>
	<div>
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
