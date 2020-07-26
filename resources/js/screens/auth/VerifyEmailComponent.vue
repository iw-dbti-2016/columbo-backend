<template>
	<div class="fixed left-0 top-0 flex items-center justify-center w-full h-screen bg-invert">
		<form @submit.prevent="" class="bg-box shadow-lg w-full max-w-md max-h-screen rounded p-4">
			<Logo class="pt-2 pb-6"></Logo>
			<p class="text-primary text-2xl leading-8">Before using your account, you have to verify your email. Please go to your inbox and click the link.</p>
			<div class="flex justify-between mt-8">
                <input class="bg-green-600 px-4 py-3 rounded text-white hover:bg-green-700 cursor-pointer" @click.prevent="checkAgain" type="submit" value="Check again">
                <input class="bg-box px-4 py-3 rounded text-fade-more hover:bg-box-fade cursor-pointer" @click.prevent="resend" type="submit" value="Resend the email">
			</div>
        </form>
	</div>
</template>

<script>
import { store } from 'Vue/store'

export default {
    name: 'verify-email',

    beforeRouteEnter(to, from, next) {
        next(component => {
            component.ready = true;

            component.stopLoading();
        })
    },

    methods: {
        checkAgain: async function() {
            await store.dispatch('auth/getUser');

            if (store.getters['auth/verified']) {
                this.$router.replace({name: 'home'});
            }
        },
        resend: function() {
            axios.post('/api/v1/auth/email/resend')
                .then((response) => {
                    console.log(response);
                });
        }
    }
}
</script>

<style>

</style>
