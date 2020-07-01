<template>
	<component v-bind:is="parsed" @click.native="clickcont" :locationables="locationables"></component>
</template>

<script>
	import LocationableOutput from 'Vue/components/locationables/LocationableOutput'

	export default {
		name: 'rich-text-output',

		props: {
			content: {
				type: String,
				default: ""
			},
			locationables: {
				type: Array,
				default: null,
			}
		},
        computed: {
            parsed() {
            	let parsedContent = dompurify.sanitize(
					this.content,
					{
						ALLOWED_TAGS: ['h1', 'h2', 'h3', 'br', 'p', 'span', 'strong', 'em', 's', 'u', 'ul', 'ol', 'li', 'blockquote', 'code', 'iframe', 'locationable-embed-item'],
						ALLOWED_ATTR: ['src', 'width', 'height', 'allowTransparancy', 'allow', 'class', 'data-id', 'data-type']
					}
				);

            	parsedContent = parsedContent.replace(
            			/<locationable-embed-item data-id="([0-9]+|[0-9a-f-]{36})" data-type="(poi|location)"[^<>]+><\/locationable-embed-item>/g,
            			"<LocationableOutput locationableid='$1' locationabletype='$2' :locationables='locationables'></LocationableOutput>"
            		)

                return {
                	props: {locationables: {type:Array, default:null,}},
                	components: {LocationableOutput},
                	template: `<div class="ProseMirror py-4 -mt-1">${parsedContent}</div>`,
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
