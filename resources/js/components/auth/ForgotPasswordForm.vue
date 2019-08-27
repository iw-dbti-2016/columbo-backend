<template>
	<div class="flex h-full max-w-md mx-auto mx-4 antialiased">
	    <div class="w-full my-auto bg-gray-100 text-gray-900 rounded-lg px-6 py-4 shadow-lg">
	        <div class="text-4xl font-semibold text-center">Reset Password</div>

	        <div>
	            <form method="POST" :action="action">
	            	<input type="hidden" name="_token" :value="csrf">
	                <div class="mt-4">
	                    <label for="email">E-Mail Address</label>
	                    <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="email" type="email" name="email" required autocomplete="email" autofocus>
	                    <span v-show="errors.email" class="text-red-600" role="alert">{{ errors.email }}</span>
	                </div>

	                <button class="block shadow block w-full mt-4 bg-blue-600 hover:bg-blue-500 focus:bg-blue-700 focus:outline-none text-white px-4 py-3 rounded-lg uppercase text-sm font-semibold tracking-wider" type="submit">
	                    Send Password Reset Link
	                </button>
	                <router-link to="/auth/login" class="inline-block mt-2 text-gray-600 hover:underline">Back to Login</router-link>

	            </form>
	        </div>
	    </div>
	</div>
</template>

<script>
	export default {
		props: ['action', 'csrf'],
		data() {
			return {
				errors: {
					email: "",
				},
			};
		},
	}
</script>
