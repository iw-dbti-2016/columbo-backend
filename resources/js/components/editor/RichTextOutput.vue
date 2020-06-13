<template>
	<component v-bind:is="parsed"></component>
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
					// remarkable.render(
					this.content
					// )
					,
					{
						ALLOWED_TAGS: ['h1', 'h2', 'h3', 'br', 'p', 'strong', 'em', 's', 'u', 'ul', 'ol', 'li', 'blockquote', 'code', 'iframe'],
						ALLOWED_ATTR: ['src', 'width', 'height', 'allowTransparancy', 'allow']
					}
				);

                return {
                	template: `<div class="ProseMirror leading-snug mt-4 text-justify markdown text-gray-100">${parsedContent}</div>`,
                };
            },
        }
    }
</script>
