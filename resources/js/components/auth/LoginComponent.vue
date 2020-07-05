<template>
	<div class="flex items-center justify-center w-full h-screen" v-if="ready">
		<form @submit.prevent="submit" class="bg-gray-900 w-full max-w-xl max-h-screen rounded p-4">
			<input v-model="form.email" type="email"><br><br>
			<input v-model="form.password" type="password"><br><br>
			<div>
				<label class="text-primary mt-3 block" for="remember">
					<input v-model="form.remember" name="remember" id="remember" class="text-primary inline-block mt-2 px-4 py-3" type="checkbox">
					<span>Remember me</span>
				</label>
			</div>
			<input @click.prevent="submit" type="submit"><br>
		</form>
	</div>
</template>

<script>
	import { mapActions } from 'vuex'

	export default {
		name: 'login',

		data() {
			return {
				form: {
					email: "",
					password: "",
					remember: false,
				},

				ready: false,
			};
		},

		beforeRouteEnter(to, from, next) {
            next(component => {
            	component.ready = true;

                component.stopLoading();
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
