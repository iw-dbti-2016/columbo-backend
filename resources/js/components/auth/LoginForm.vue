<template>
	<div class="flex h-full max-w-4xl mx-auto mx-4 antialiased">
		<div class="my-auto hidden lg:block w-1/2">
	        <h1 class="text-2xl text-center">Go Forward<br><span class="text-blue-600 font-semibold">Don't look back!</span></h1>
	        <img :src="source + '/' + image" :alt="alt">
	    </div>
		<div class="my-auto lg:ml-8 bg-gray-100 text-gray-900 md:w-1/2 mx-auto rounded-lg px-6 py-4 shadow-lg">
	        <h1 class="text-center font-semibold text-4xl">TravelCompanion</h1>
	        <div class="mt-4 font-sans font-semibold text-lg">Login</div>

	        <div class="mt-2">
	            <form method="POST" :action="action">
	            	<input type="hidden" name="_token" :value="csrf">
	                <div>
	                    <label for="email">Email</label>
                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="email" type="email" name="email" required autocomplete="email" autofocus>
                        <span v-show="errors.email" class="text-red-600" role="alert">{{ errors.email }}</span>
	                </div>

	                <div class="mt-2">
	                    <label for="password">Password</label>
                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="password" type="password" name="password" required autocomplete="current-password">
                        <span v-show="errors.password" class="text-red-600" role="alert">{{ errors.password }}</span>
	                </div>

	                <div class="mt-2">
	                    <input type="checkbox" name="remember" id="remember">
	                    <label for="remember">Remember Me</label>
	                </div>

	                <div>
	                    <button class="shadow block w-full mt-2 bg-blue-600 hover:bg-blue-500 focus:bg-blue-700 focus:outline-none text-white px-4 py-3 rounded-lg uppercase text-sm font-semibold tracking-wider" type="submit">
	                        Login
	                    </button>

	                    <div class="mt-2 flex text-gray-600 align-baseline justify-between">
	                        <router-link to="/auth/register" class="inline-block hover:underline">Sign up now</router-link>
	                        <router-link to="/auth/forgot-password" class="inline-block hover:underline">Forgot Your Password?</router-link>
	                    </div>
	                </div>
	            </form>
	        </div>
	    </div>
	</div>
</template>

<script>
	export default {
		props: ['action', 'source', 'image', 'alt', 'csrf'],
		data() {
			return {
				errors: {
					email: "",
					password: "",
				},
			};
		},
	}
</script>
