<template>
	<div class="flex items-center justify-center w-full h-screen" v-if="ready">
		<form @submit.prevent="submit" class="bg-gray-900 w-full max-w-xl max-h-screen rounded p-4">
			<input v-model="form.email" type="email"><br><br>
			<input v-model="form.password" type="password"><br><br>
			<input @click.prevent="submit" type="submit"><br>
		</form>
	</div>
</template>

<script>
	import NProgress from 'nprogress'
	import { mapActions } from 'vuex'

	export default {
		name: 'login',

		data() {
			return {
				form: {
					email: "",
					password: "",
				},

				ready: false,
			};
		},

		beforeRouteEnter(to, from, next) {
            next(component => {
            	component.ready = true;

                NProgress.done()
            })
        },

		methods: {
			...mapActions({
				login: 'auth/login'
			}),

			async submit() {
				await this.login(this.form)

				this.$router.replace({name: 'home'});
			}
		}
	}
</script>
