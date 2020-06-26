import { Node } from 'tiptap'

export default class DragItem extends Node {

	get name() {
		return 'locationable_embed'
	}

	get schema() {
		return {
			attrs: {
				title: {
					default: null,
				},
				type: {
					default: null,
				},
				id: {
					default: null,
				},
			},
			group: 'block',
			draggable: true,
			content: 'paragraph+',
			toDOM: node => ['locationable-embed-item',
				{
					'data-use': this.name,
					'data-title': node.attrs.title,
					'data-type': node.attrs.type,
					'data-id': node.attrs.id,
				},
			],
			parseDOM: [{
				tag: `[data-use="${this.name}"]`,
				getAttrs: dom => ({
					title: dom.getAttribute('data-title'),
					type: dom.getAttribute('data-type'),
					id: dom.getAttribute('data-id'),
				}),
			}],
		}
	}

	get view() {
		return {
			props: ['node', 'updateAttrs', 'view'],
			computed: {
				title: {
					get() {
						return this.node.attrs.title
					},
					set(title) {
						this.updateAttrs({
							title,
						})
					},
				},
				type: {
					get() {
						return this.node.attrs.type
					},
					set(type) {
						this.updateAttrs({
							type,
						})
					},
				},
				id: {
					get() {
						return this.node.attrs.id
					},
					set(id) {
						this.updateAttrs({
							id,
						})
					},
				},
			},
			template: `
				<div data-type="locationable_embed" :data-id="id" :data-type="type" contenteditable="false" class="my-6">
					<div class="flex justify-between bg-box rounded-lg px-2 py-2">
						<div class="flex items-center min-w-0">
							<div class="flex-shrink-0 w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
								title="Location">
								<font-awesome-icon v-if="type === 'location'" class="text-2xl" :icon="['fas', 'map-marker-alt']" />
								<font-awesome-icon v-else class="text-yellow-600 text-2xl" :icon="['fas', 'star']" />
							</div>
							<div class="h-full flex items-center text-primary text-lg overflow-hidden whitespace-no-wrap">{{ title }}</div>
						</div>
						<div class="flex items-center">
							<a data-action="locationable-edit" :data-l-id="id" v-if="type === 'location'"
									class="ml-2 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
									title="Edit">
								<font-awesome-icon class="text-2xl" :icon="['fas', 'pen']" />
							</a>
							<a data-action="locationable-detach" :data-l-id="id"
									class="ml-2 cursor-pointer focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
									title="Detach">
								<font-awesome-icon class="text-2xl" :icon="['fas', 'times']" />
							</a>
							<div data-drag-handle
									class="ml-2 flex-shrink-0 cursor-move focus:outline-none focus:text-fade hover:text-fade hover:bg-box-fade w-12 h-12 rounded-full text-fade-more flex items-center justify-center"
									title="Drag">
								<font-awesome-icon class="text-2xl" :icon="['fas', 'grip-lines']" />
							</div>
						</div>
					</div>
				</div>
			`,
		}
	}

	commands({ type }) {
		return {
			locationable_embed: attrs => this.insert(attrs, type),
			detach_locationable: attrs => this.deleteNode(attrs),
		};
	}

	insert(attrs, type) {
		return (state, dispatch) => {
			const { selection } = state;
			const position = selection.$cursor
				? selection.$cursor.pos
				: selection.$to.pos;

			const node = type.create(attrs);
			const transaction = state.tr.insert(position, node);

			dispatch(transaction);
		};
	}

	deleteNode(attrs) {
		return (state, dispatch) => {
			const { selection } = state;
			const position = selection.$cursor
					? selection.$cursor.pos
					: selection.$to.pos;

			const transaction = state.tr.delete(position, position + 5)
			dispatch(transaction)
		};
	}
}
