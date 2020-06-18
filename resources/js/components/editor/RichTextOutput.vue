<template>
	<component v-bind:is="parsed" @click.native="clickcont"></component>
</template>

<script>
	export default {
		name: 'rich-text-output',

		props: {
			content: {
				type: String,
				default: ""
			},
		},
        computed: {
            parsed() {
            	let parsedContent = dompurify.sanitize(
					this.content,
					{
						ALLOWED_TAGS: ['h1', 'h2', 'h3', 'br', 'p', 'span', 'strong', 'em', 's', 'u', 'ul', 'ol', 'li', 'blockquote', 'code', 'iframe'],
						ALLOWED_ATTR: ['src', 'width', 'height', 'allowTransparancy', 'allow', 'class']
					}
				);

                return {
                	template: `<div class="ProseMirror pt-1 mt-5">${parsedContent}</div>`,
                };
            },
        },
        methods: {
        	clickcont(clickInfo) {
				if (! clickInfo.srcElement.classList.contains("mention")) {
					return
				}

				let tagUsername = clickInfo.srcElement.getAttribute("data-mention-id");

				if (tagUsername === null) {
					return
				}

				this.$router.push({name: 'showProfile', params: {username: tagUsername}})
			}
		}
	}
</script>
