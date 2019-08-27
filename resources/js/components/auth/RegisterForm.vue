<template>
	<div class="flex h-full max-w-4xl mx-auto mx-4 antialiased">
		<div class="my-auto hidden lg:block w-1/2 text-center">
	        <h1 class="text-gray-900 text-2xl">Because <span class="text-6xl text-blue-600">β</span>ugs can happen</h1>
        	<img :src="source + '/' + image" :alt="alt">
	    </div>
		<div class="my-auto lg:ml-8 bg-gray-100 text-gray-900 md:w-1/2 mx-auto rounded-lg px-6 py-4 shadow-lg">
	        <div class="text-4xl font-semibold text-center">Sign up for the <span class="text-blue-600">β</span></div>

	        <div class="mt-2">
	            <form method="POST" :action="action">
	            	<input type="hidden" name="_token" :value="csrf">
	                <div class="mt-4">
	                    <div class="flex justify-between">
	                        <div class="w-4/12">
	                            <label for="first_name">First Name*</label>
	                            <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-11/12" id="first_name" type="text" name="first_name" required autocomplete="first_name" autofocus>

	                            <span v-show="errors.firstName" class="text-red-600" role="alert">{{ errors.firstName }}</span>
	                        </div>
	                        <div class="w-2/12">
	                            <label for="middle_name">Middle</label>
	                            <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-10/12" id="middle_name" type="text" name="middle_name" autocomplete="middle_name" autofocus>

	                            <span v-show="errors.middleName" class="text-red-600" role="alert">{{ errors.middleName }}</span>
	                        </div>
	                        <div class="w-6/12">
	                            <label for="last_name">Last Name*</label>
	                            <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="last_name" type="text" name="last_name" required autocomplete="last_name" autofocus>

	                            <span v-show="errors.lastName" class="text-red-600" role="alert">{{ errors.lastName }}</span>
	                        </div>
	                    </div>
	                </div>

	                <div class="mt-2">
	                    <label for="username">Username*</label>

	                    <div>
	                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="username" type="username" name="username" required autocomplete="username">

	                        <span v-show="errors.username" class="text-red-600" role="alert">{{ errors.username }}</span>
	                    </div>
	                </div>

	                <div class="mt-2">
	                    <label for="email">E-Mail Address*</label>

	                    <div>
	                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="email" type="email" name="email" required autocomplete="email">

	                        <span v-show="errors.email" class="text-red-600" role="alert">{{ errors.email }}</span>
	                    </div>
	                </div>

	                <div class="mt-2">
	                    <label for="home_location">Home Location*</label>

	                    <div>
	                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="home_location" type="text" name="home_location" required autocomplete="home_location">

	                        <span v-show="errors.homeLocation" class="text-red-600" role="alert">{{ errors.homeLocations }}</span>
	                    </div>
	                </div>

	                <div class="mt-2">
	                    <label for="password">Password*</label>

	                    <div>
	                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="password" type="password" name="password" required autocomplete="new-password">

	                        <span v-show="errors.password" class="text-red-600" role="alert">{{ errors.password }}</span>
	                    </div>
	                </div>

	                <div class="mt-2">
	                    <label for="password-confirm">Confirm Password*</label>

	                    <div>
	                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
	                    </div>
	                </div>

	                <button class="block shadow block w-full mt-4 bg-blue-600 hover:bg-blue-500 focus:bg-blue-700 focus:outline-none text-white px-4 py-3 rounded-lg uppercase text-sm font-semibold tracking-wider"  type="submit">
	                    Register
	                </button>
	                <div class="text-right">
	                    <router-link to="/auth/login" class="inline-block mt-2 text-gray-600 hover:underline">Already signed up? Login</router-link>
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
					firstName: "",
					middleName: "",
					lastName: "",
					username: "",
					email: "",
					homeLocation: "",
					password: "",
				},
			};
		},
	}
</script>
