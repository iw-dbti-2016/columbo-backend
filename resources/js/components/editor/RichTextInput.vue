<template>
	<div class="editor mt-6">
		<editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
			<div class="menubar is-hidden flex bg-primary px-2 py-2 pb-4 sticky top-0 z-10 border-b border-box-fade">
				<EditorMenuButton icon="bold" :isActive="isActive.bold()" @click="commands.bold" title="Bold"></EditorMenuButton>
				<EditorMenuButton icon="italic" :isActive="isActive.italic()" @click="commands.italic" title="Italic"></EditorMenuButton>
				<EditorMenuButton icon="strikethrough" :isActive="isActive.strike()" @click="commands.strike" title="Strikthrough"></EditorMenuButton>
				<EditorMenuButton icon="underline" :isActive="isActive.underline()" @click="commands.underline" title="Underline"></EditorMenuButton>
				<EditorMenuButton icon="code" :isActive="isActive.code()" @click="commands.code" title="Inline Code"></EditorMenuButton>
				<EditorMenuButton icon="paragraph" :isActive="isActive.paragraph()" @click="commands.paragraph" title="Paragraph"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="1" :isActive="isActive.heading({level:1})" @click="commands.heading({level:1})" title="Large Heading"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="2" :isActive="isActive.heading({level:2})" @click="commands.heading({level:2})" title="Medium Heading"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="3" :isActive="isActive.heading({level:3})" @click="commands.heading({level:3})" title="Small Heading"></EditorMenuButton>
				<EditorMenuButton icon="list-ul" :isActive="isActive.bullet_list()" @click="commands.bullet_list" title="Bullet list"></EditorMenuButton>
				<EditorMenuButton icon="list-ol" :isActive="isActive.ordered_list()" @click="commands.ordered_list" title="Ordered list"></EditorMenuButton>
				<EditorMenuButton icon="quote-left" :isActive="isActive.blockquote()" @click="commands.blockquote" title="Blockquote"></EditorMenuButton>
				<EditorMenuButton icon="spotify" iconType="b" @click="showSpotifyModal(commands.spotify_track_embed)" title="Spotify Track"></EditorMenuButton>
				<EditorMenuButton icon="map-marker-alt" @click="showLocationableModal(commands.locationable_embed)" title="Location or POI"></EditorMenuButton>
				<EditorMenuButton icon="undo-alt" @click="commands.undo" title="Undo"></EditorMenuButton>
				<EditorMenuButton icon="redo-alt" @click="commands.redo" title="Redo"></EditorMenuButton>
				<span v-show="maxLength > 0 && content.length > maxLength" class="absolute font-bold text-red-500 text-xl right-0 top-0 mr-2 mt-3">{{label}} Too Long!</span>
			</div>
		</editor-menu-bar>

		<editor-floating-menu :editor="editor" v-slot="{ commands, isActive, menu }">
			<div class="floating-menu-bar flex" :class="{ 'is-active': menu.isActive }" :style="`top: ${menu.top}px`">
				<div class="w-2"></div> <!-- PADDING -->
				<EditorMenuButton icon="heading" postFix="1" :isActive="isActive.heading({level:1})" @click="commands.heading({level:1})" title="Large Heading"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="2" :isActive="isActive.heading({level:2})" @click="commands.heading({level:2})" title="Medium Heading"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="3" :isActive="isActive.heading({level:3})" @click="commands.heading({level:3})" title="Small Heading"></EditorMenuButton>
				<EditorMenuButton icon="list-ul" :isActive="isActive.bullet_list()" @click="commands.bullet_list" title="Bullet list"></EditorMenuButton>
				<EditorMenuButton icon="list-ol" :isActive="isActive.ordered_list()" @click="commands.ordered_list" title="Ordered list"></EditorMenuButton>
				<EditorMenuButton icon="quote-left" :isActive="isActive.blockquote()" @click="commands.blockquote" title="Blockquote"></EditorMenuButton>
				<EditorMenuButton icon="spotify" iconType="b" @click="showSpotifyModal(commands.spotify_track_embed)" title="Spotify Track"></EditorMenuButton>
				<EditorMenuButton icon="map-marker-alt" @click="showLocationableModal(commands.locationable_embed)" title="Location or POI"></EditorMenuButton>
			</div>
		</editor-floating-menu>

		<EmbedSpotifyTrackModal ref="spotifyModal" @onConfirm="insertSpotifyEmbed"></EmbedSpotifyTrackModal>
		<EmbedLocationableModal ref="locationableModal" @onConfirm="insertLocationableEmbed"></EmbedLocationableModal>

		<div class="bg-box shadow-xl p-2 text-base font-bold rounded-md" v-show="showSuggestions" ref="suggestions">
			<template v-if="hasResults">
				<div v-for="(user, index) in filteredUsers" :key="user.username"
				class="rounded px-2 py-2 mb-1 cursor-pointer last:mb-0 text-primary hover:bg-box-fade"
				:class="{ 'bg-box-fade': navigatedUserIndex === index }"
				@click="selectUser(user)">
					{{ user.name }}
				</div>
			</template>
			<div v-else class="rounded-lg px-2 py-1 mb-0 cursor-pointer text-fade-more">
				No users found
			</div>
		</div>

		<editor-content @click.native="clickeditor" :editor="editor" class="mt-2 pb-2 border-b border-box-fade ProseMirror-editor"></editor-content>
	</div>
</template>

<script>
	import Fuse from 'fuse.js'
	import tippy, { sticky } from 'tippy.js'
	import { Editor, EditorContent, EditorFloatingMenu, EditorMenuBar } from 'tiptap'
	import {
		Blockquote, BulletList, HardBreak, Mention,
		Heading, ListItem, OrderedList, Strike, Underline,
		Bold, Code, Italic, TrailingNode, Placeholder, History } from 'tiptap-extensions'

	import SpotifyTrackEmbed from './SpotifyTrackEmbed.js'
	import EmbedSpotifyTrackModal from './EmbedSpotifyTrackModal'
	import EditorMenuButton from './EditorMenuButton'
	import DragItem from './DragItem.js'
	import EmbedLocationableModal from './EmbedLocationableModal'

	export default {
		name: 'rich-text-input',

		components: {
			EditorContent,
			EditorFloatingMenu,
			EditorMenuBar,
			EmbedSpotifyTrackModal,
			EditorMenuButton,
			EmbedLocationableModal,
		},

		props: {
			content: {
				type: String,
				default: "",
			},
			label: {
				type: String,
				default: "Content",
			},
			tagSuggestions: {
				type: Array,
				default: function() {
					return []
				},
			},
			maxLength: {
				type: Number,
				default: -1,
			}
		},

		data() {
			return {
				editor: new Editor({
					content: this.content,
					extensions: [
						// CUSTOM
						new SpotifyTrackEmbed(),
						new DragItem(),

						// BASIC DEFAULT
						new Blockquote(), new BulletList(),  new HardBreak(),
						new ListItem(),   new OrderedList(), new Strike(),    new Underline(),
						new Bold(),       new Code(),        new Italic(),    new History(),

						// CONFIGURABLE DEFAULT
						new Heading({ levels: [1, 2, 3] }),
						new TrailingNode({
							node: 'paragraph',
							notAfter: ['paragraph'],
						}),
						new Placeholder({
							showOnlyCurrent: false,
							emptyNodeText: node => {
								return  `Start writing the ${this.label.toLowerCase()} right here!`
							},
						}),
						new Mention({
							items: () => this.tagSuggestions,

							onEnter: ({ items, query, range, command, virtualNode }) => {
								this.query = query
								this.filteredUsers = items
								this.suggestionRange = range
								this.renderPopup(virtualNode)

								this.insertMention = command
							},

							onChange: ({ items, query, range, virtualNode }) => {
								this.query = query
								this.filteredUsers = items
								this.suggestionRange = range
								this.navigatedUserIndex = 0
								this.renderPopup(virtualNode)
							},

							onExit: () => {
								this.query = null
								this.filteredUsers = []
								this.suggestionRange = null
								this.navigatedUserIndex = 0
								this.destroyPopup()
							},

							onKeyDown: ({ event }) => {
								if (event.key === 'ArrowUp') {
									this.upHandler()
									return true
								}
								if (event.key === 'ArrowDown') {
									this.downHandler()
									return true
								}
								if (event.key === 'Enter') {
									this.enterHandler()
									return true
								}
								return false
							},

							onFilter: (items, query) => {
								if (!query) {
									return items
								}
								const fuse = new Fuse(items, {
									threshold: 0.2,
									keys: ['name'],
								})
								return fuse.search(query).map(item => item.item);
							},
						}),
					],
					onUpdate: ({ state, getHTML, getJSON, transaction }) => {
						this.$emit('update:content', getHTML());
					},
				}),
				query: null,
				suggestionRange: null,
				filteredUsers: [],
				navigatedUserIndex: 0,
				insertMention: () => {},
			};
		},

		beforeDestroy() {
			this.destroyPopup()
			this.editor.destroy()
		},

		computed: {
			hasResults() {
				return this.filteredUsers.length
			},
			showSuggestions() {
				return this.query || this.hasResults
			},
		},

		methods: {
			// This was for support of switching between full name and
			// 	just first name, but it doesn't work because tiptap
			// 	generates the html based on the state of the plugin
			// 	and not the content of the editor.
			clickeditor(clickInfo) {
				let el = clickInfo.srcElement;

				while (el.tagName !== "DIV") {
					if (el.tagName === "A") {
						if (el.getAttribute('data-action') !== null) {
							let locationable = document.querySelector(`[data-id="${el.getAttribute('data-l-id')}"]`)

							let lId = locationable.getAttribute('data-id');
							let lType = locationable.getAttribute('data-type');

							this.$emit('detachlocationable', {type: lType, id: lId});
							locationable.remove();
						} else {
							break;
						}
					}
					el = el.parentElement;
				}

				// let span = clickInfo.srcElement;

				// if (! span.classList.contains("mention")) {
				// 	return
				// }
				//
				// if (span.hasAttribute("data-mention-fulltext")) {
				// 	let fulltext = span.getAttribute("data-mention-fulltext")
				// 	span.removeAttribute("data-mention-fulltext")
				// 	span.innerText = fulltext
				// } else {
				// 	span.setAttribute("data-mention-fulltext", span.innerText)
				// 	span.innerText = span.innerText.split(' ')[0]
				// }
			},

			// SPOTIFY
			showSpotifyModal(command) {
				this.$refs.spotifyModal.showModal(command)
			},
			insertSpotifyEmbed(data) {
				if (data.command !== null) {
					data.command(data.data)
				}
			},

			// Locationable
			showLocationableModal(command) {
				this.$refs.locationableModal.showModal(command)
			},
			insertLocationableEmbed(data) {
				if (data.command !== null) {
					data.command(data.data)
				}
				this.$emit('selectlocationable', data.data);
			},

			// MENTIONS
			upHandler() {
				this.navigatedUserIndex = ((this.navigatedUserIndex + this.filteredUsers.length) - 1) % this.filteredUsers.length
			},
			downHandler() {
				this.navigatedUserIndex = (this.navigatedUserIndex + 1) % this.filteredUsers.length
			},
			enterHandler() {
				const user = this.filteredUsers[this.navigatedUserIndex]
				if (user) {
					this.selectUser(user)
				}
			},
			selectUser(user) {
				this.insertMention({
					range: this.suggestionRange,
					attrs: {
						id: user.username,
						label: user.name,
					},
				})
				this.editor.focus()
			},
			renderPopup(node) {
				if (this.popup) {
					return
				}
				// ref: https://atomiks.github.io/tippyjs/v6/all-props/
				this.popup = tippy('.page', {
					getReferenceClientRect: node.getBoundingClientRect,
					interactive: true,
					sticky: true, // make sure position of tippy is updated when content changes
					plugins: [sticky],
					content: this.$refs.suggestions,
					trigger: 'mouseenter', // manual
					showOnCreate: true,
					placement: 'top-start',
					appendTo: () => document.getElementById("parent-element"),
				})
			},
			destroyPopup() {
				if (this.popup) {
					this.popup[0].destroy()
					this.popup = null
				}
			},
		}
	}
</script>

<style lang="scss">
	.editor *:not(.ProseMirror-focused) > .is-empty:nth-child(1)::before {
		content: attr(data-empty-text);
	}
</style>
