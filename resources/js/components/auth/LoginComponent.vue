<template>

	<div class="flex items-center justify-center w-full h-screen">
		<form @submit.prevent="submit" class="bg-gray-900 w-full max-w-xl max-h-screen rounded p-4">
			<input v-model="form.email" type="email"><br><br>
			<input v-model="form.password" type="password"><br><br>
			<input @click.prevent="submit" type="submit"><br>
		</form>
	</div>
</template>

<script>
	import { mapActions } from 'vuex'

	export default {
		name: 'Login',

		data() {
			return {
				form: {
					email: "",
					password: "",
				}
			};
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
