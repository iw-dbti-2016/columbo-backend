<template>
	<div class="m-auto pl-8 pr-24 w-full" v-if="ready">
		<ActionBarComponent :title="`This is you, ${user.first_name}`"></ActionBarComponent>
        <h1 class="text-6xl tracking-wide uppercase">@{{ user.username }}</h1>
        <pre>
            {{ user }}
        </pre>
	</div>
</template>

<script>
	import NProgress from 'nprogress'

	export default {
		name: 'show-profile',

        data() {
            return {
                user: {},
            	ready: false,
            };
        },

        beforeRouteEnter(to, from, next) {
            next(component => {
            	let username = component.$route.params.username;

                axios.get(`/api/v1/users/${username}`)
                    .then(response => {
                    	component.user = response.data;
                    	component.ready = true;

                        NProgress.done()
                    })
                    .catch(component.handleError)
            })
        },

        methods: {
            handleError: function(error) {
            	if (error.response.status == 500 || error.response.status == 403) {
                    this.userData = error.response.data;
                }
                if (error.response.status == 401) {
                    document.getElementById('logout').submit();
                }
                console.log("error: " + error);
            },
        },
    }
</script>
