<template>
	<div class="editor mt-6">
		<editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
			<div class="menubar is-hidden flex bg-gray-900 px-2 py-2 pb-4 sticky top-0 z-10 border-b border-gray-700">
				<EditorMenuButton icon="bold" :isActive="isActive.bold()" @click="commands.bold"></EditorMenuButton>
				<EditorMenuButton icon="italic" :isActive="isActive.italic()" @click="commands.italic"></EditorMenuButton>
				<EditorMenuButton icon="strikethrough" :isActive="isActive.strike()" @click="commands.strike"></EditorMenuButton>
				<EditorMenuButton icon="underline" :isActive="isActive.underline()" @click="commands.underline"></EditorMenuButton>
				<EditorMenuButton icon="code" :isActive="isActive.code()" @click="commands.code"></EditorMenuButton>
				<EditorMenuButton icon="paragraph" :isActive="isActive.paragraph()" @click="commands.paragraph"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="1" :isActive="isActive.heading({level:1})" @click="commands.heading({level:1})"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="2" :isActive="isActive.heading({level:2})" @click="commands.heading({level:2})"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="3" :isActive="isActive.heading({level:3})" @click="commands.heading({level:3})"></EditorMenuButton>
				<EditorMenuButton icon="list-ul" :isActive="isActive.bullet_list()" @click="commands.bullet_list"></EditorMenuButton>
				<EditorMenuButton icon="list-ol" :isActive="isActive.ordered_list()" @click="commands.ordered_list"></EditorMenuButton>
				<EditorMenuButton icon="quote-left" :isActive="isActive.blockquote()" @click="commands.blockquote"></EditorMenuButton>
				<EditorMenuButton icon="spotify" iconType="b" @click="showSpotifyModal(commands.spotify_track_embed)"></EditorMenuButton>
			</div>
		</editor-menu-bar>

		<editor-floating-menu :editor="editor" v-slot="{ commands, isActive, menu }">
			<div class="floating-menu-bar flex" :class="{ 'is-active': menu.isActive }" :style="`top: ${menu.top}px`">
				<div class="w-2"></div> <!-- PADDING -->
				<EditorMenuButton icon="heading" postFix="1" :isActive="isActive.heading({level:1})" @click="commands.heading({level:1})"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="2" :isActive="isActive.heading({level:2})" @click="commands.heading({level:2})"></EditorMenuButton>
				<EditorMenuButton icon="heading" postFix="3" :isActive="isActive.heading({level:3})" @click="commands.heading({level:3})"></EditorMenuButton>
				<EditorMenuButton icon="list-ul" :isActive="isActive.bullet_list()" @click="commands.bullet_list"></EditorMenuButton>
				<EditorMenuButton icon="list-ol" :isActive="isActive.ordered_list()" @click="commands.ordered_list"></EditorMenuButton>
				<EditorMenuButton icon="quote-left" :isActive="isActive.blockquote()" @click="commands.blockquote"></EditorMenuButton>
				<EditorMenuButton icon="spotify" iconType="b" @click="showSpotifyModal(commands.spotify_track_embed)"></EditorMenuButton>
			</div>
		</editor-floating-menu>

		<EmbedSpotifyTrackModal ref="spotifyModal" @onConfirm="insertSpotifyEmbed"></EmbedSpotifyTrackModal>

		<div class="bg-gray-800 shadow-xl p-2 text-base font-bold rounded-md" v-show="showSuggestions" ref="suggestions">
			<template v-if="hasResults">
				<div v-for="(user, index) in filteredUsers" :key="user.username"
				class="rounded px-2 py-2 mb-1 cursor-pointer last:mb-0 hover:bg-gray-700"
				:class="{ 'bg-gray-700': navigatedUserIndex === index }"
				@click="selectUser(user)">
					{{ user.name }}
				</div>
			</template>
			<div v-else class="rounded-lg px-2 py-1 mb-0 cursor-pointer text-gray-400">
				No users found
			</div>
		</div>

		<editor-content :editor="editor" class="mt-2 pb-2 border-b border-gray-700"></editor-content>
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

	export default {
		name: 'rich-text-input',

		components: {
			EditorContent,
			EditorFloatingMenu,
			EditorMenuBar,
			EmbedSpotifyTrackModal,
			EditorMenuButton,
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
		},

		data() {
			return {
				editor: new Editor({
					content: this.content,
					extensions: [
						// CUSTOM
						new SpotifyTrackEmbed(),
						// DEFAULT
						new Blockquote(),
						new BulletList(),
						new HardBreak(),
						new ListItem(),
						new OrderedList(),
						new Strike(),
						new Underline(),
						new Bold(),
						new Code(),
						new Italic(),
						new History(),
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

							onEnter: ({
								items, query, range, command, virtualNode,
							}) => {
								this.query = query
								this.filteredUsers = items
								this.suggestionRange = range
								this.renderPopup(virtualNode)

								this.insertMention = command
							},

							onChange: ({
								items, query, range, virtualNode,
							}) => {
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
					onUpdate: ( { state, getHTML, getJSON, transaction } ) => {
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
			// SPOTIFY
			showSpotifyModal(command) {
				this.$refs.spotifyModal.showModal(command)
			},
			insertSpotifyEmbed(data) {
				if (data.command !== null) {
					data.command(data.data)
				}
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
					appendTo: () => document.body,
					interactive: true,
					sticky: true, // make sure position of tippy is updated when content changes
					plugins: [sticky],
					content: this.$refs.suggestions,
					trigger: 'mouseenter', // manual
					showOnCreate: true,
					theme: 'dark',
					placement: 'top-start',
					inertia: true,
					duration: [400, 200],
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
