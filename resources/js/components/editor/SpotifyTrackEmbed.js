import { Node } from 'tiptap'

export default class SpotifyTrackEmbed extends Node {
	get name() {
		return 'spotify_track_embed'
	}

	get schema() {
		return {
			attrs: {
				src: {
					default: null,
				},
			},
			group: 'block',
			selectable: false,
			parseDOM: [{
				tag: 'iframe',
				getAttrs: dom => ({
					src: dom.getAttribute('src'),
				}),
			}],
			toDOM: node => ['iframe', {
				src: node.attrs.src,
				width: '100%',
				height: '80',
				frameborder: '0',
				allowtransparency: 'true',
				allow: 'encrypted-media',
				// class: 'rounded-lg',
			}],
		}
	}

	get view() {
		return {
			props: ['node', 'updateAttrs', 'view'],
			computed: {
				src: {
					get() {
						return this.node.attrs.src
					},
					set(src) {
						this.updateAttrs({
							src,
						})
					},
				},
			},
			template: `
				<div class="iframe">
					<iframe :src="src" width="100%" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media" class="rounded"></iframe>
				</div>
			`,
		}
	}

	commands({ type }) {
		return attrs => (state, dispatch) => {
			const { selection } = state;
			const position = selection.$cursor
				? selection.$cursor.pos
				: selection.$to.pos;

			const node = type.create(attrs);
			const transaction = state.tr.insert(position, node);

			dispatch(transaction);
		};
	}
}
