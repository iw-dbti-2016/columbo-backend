<template>
	<component v-bind:is="parsed"></component>
</template>

<script>
	export default {
		props: {
			content: {
				type: String,
				default: ""
			},
		},
        computed: {
            parsed() {
            	let parsedContent = dompurify.sanitize(
					remarkable.render(this.content),
					{
						ALLOWED_TAGS: ['router-link', 'a', 'h1', 'h2', 'h3', 'br', 'hr', 'p', 'strong', 'em', 'iframe'],
						ALLOWED_ATTR: [':to', 'href', 'src', 'width', 'height', 'allowTransparancy', 'allow']
					}
				);

                return {
                	template: `<div class="leading-snug mt-4 text-justify markdown">${parsedContent}</div>`,
                };
            },
        }
    }
</script>
