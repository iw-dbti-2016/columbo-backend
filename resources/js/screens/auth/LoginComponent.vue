<template>
	<div class="fixed left-0 top-0 flex items-center justify-center w-full h-screen bg-invert" v-if="ready">
		<form @submit.prevent="submit" class="bg-box shadow-lg w-full max-w-md max-h-screen rounded p-4">
			<Logo class="pt-2 pb-4"></Logo>
			<FormInput label="E-mail address" type="email" v-model="form.email" :fade="true"></FormInput>
			<FormInput label="Password" type="password" v-model="form.password" :fade="true"></FormInput>
			<CheckboxInput class="mt-4" title="Remember me" v-model="form.remember" :fade="true"></CheckboxInput>
			<input class="mt-4 bg-green-600 px-4 py-3 rounded text-white hover:bg-green-700 cursor-pointer" @click.prevent="submit" type="submit" value="Log in">
		</form>
	</div>
</template>

<script>
	import { mapActions } from 'vuex'
	import FormInput from 'Vue/components/forms/FormInput'
	import CheckboxInput from 'Vue/components/forms/CheckboxInput'

	export default {
		name: 'login',

		components: {
			FormInput,
			CheckboxInput,
		},

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
