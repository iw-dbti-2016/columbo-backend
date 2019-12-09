<template>
	<div class="m-auto max-w-4xl my-8 py-10 w-full relative">
        <h1 class="text-6xl tracking-wide uppercase">@{{ user.username }}</h1>
        <pre>
            {{ user }}
        </pre>
	</div>
</template>

<script>
	export default {
		mounted() {

        },
        data() {
            return {
            	loading: true,
                user: {},
            };
        },
        created() {
        	this.getUser();
        },
        methods: {
            getUser: function() {
            	let username = this.$route.params.username;

                axios.get(`/api/v1/users/${username}`)
                    .then((response) => {
                        this.user = response.data.data;
                        this.loading = false;
                    })
                    .catch((error) => {
                        if (error.response.status == 500 || error.response.status == 403) {
                            this.userData = error.response.data;
                        }
                        if (error.response.status == 401) {
                            document.getElementById('logout').submit();
                        }
                        console.log("error: " + error);
                    });
            },
        },
    }
</script>
