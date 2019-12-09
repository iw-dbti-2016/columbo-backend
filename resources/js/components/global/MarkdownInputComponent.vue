<template>
	<div>
		<div v-if="suggestions.length" class="fixed top-0 h-full left-0 ml-24 flex flex-col justify-center">
			<div @click.prevent="addTag" class="bg-gray-100 h-9 px-2 py-2 rounded shadow sticky text-left text-xl w-56" v-html="suggestions"></div>
		</div>
		<label class="text-gray-700 mt-3 block" for="content">{{ label }}</label>
		<div class="mt-2">
			<a :class="{'font-bold': !preview}" class="bg-gray-100 inline-block mr-1 px-8 py-4 rounded shadow hover:shadow-md" @click.prevent="preview = false" href="#">Edit</a>
			<a :class="{'font-bold': preview}" class="bg-gray-100 inline-block px-8 py-4 rounded shadow hover:shadow-md" @click.prevent="switchToPreview" href="#">Preview</a>
		</div>
		<div v-if="!preview" @focus="suggestions = '';" @blur="" @click="onClick" @keyup="onKeyUp" id="edit-box" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" contenteditable="true" class="inline-block whitespace-pre-wrap leading-tight resize-y w-full h-64 overflow-y-auto bg-gray-100 block mt-2 px-4 py-3 shadow rounded focus:outline-none focus:shadow-md" v-html="outOfSyncInputData"></div>
		<div v-else>
			<MarkdownOutputComponent :content="markdownData"></MarkdownOutputComponent>
		</div>
		<div>
			<span></span>
			<span></span>
		</div>
	</div>
</template>

<script>
	export default {
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
					return [
						{
							first_name: "John",
							middle_name: null,
							last_name: "Doe",
							username: "johndoe",
						},
						{
							first_name: "Jonas",
							middle_name: "R",
							last_name: "Donzo",
							username: "jonasrdonzo",
						}
					];
				},
			},
		},
		data() {
			return {
				click: false,

				cursorPosition: 0,
				contentLength: 0,
				focus: false,
				atSymbols: [], // Parsed on every insert
				tags: [], // Parsed at the beginning when loading data, positions updated on every insert
				//
				// {
				// 		textPosition
				// 		(MarkdownPosition) => don't really care about this
				// 		htmlPosition
				// 		textLength
				// 		htmlLength
				// 		tagText
				// 		tagMarkdown
				// 		tagHtml
				// 		username
				// }

				markdownData: this.content,
				htmlData: "",
				outOfSyncInputData: "",
				preview: false,
				suggestions: "",

			};
		},
		beforeMount() {
			this.markdownData = this.content;
			this.outOfSyncInputData = this.initTagData(this.content);
			this.htmlData = this.markdownToHtml(this.content);
		},
		watch: {
			content: function(value) {
				this.markdownData = value;

				if (this.outOfSyncInputData.length === 0) {
					this.outOfSyncInputData = this.initTagData(value);
				}

				this.htmlData = this.markdownToHtml(value);
			}
		},
		methods: {
			///////////////////////
			// BASIC CONVERSIONS //
			///////////////////////
			markdownToHtml(markdown) {
				let tagRegex = new RegExp("@([A-Za-z-'\. ]+):([A-Za-z0-9-\.]{4,40});", "g");

				return tagRegex.exec(markdown, '<span contenteditable="false" data-username="$2" class="bg-gray-200 px-1 rounded inline-block">@$1</span>');
			},
			markdownToText(markdown) {
				let tagRegex = new RegExp("@([A-Za-z-'\. ]+):([A-Za-z0-9-\.]{4,40});", "g");

				return tagRegex.exec(markdown, "@$1");
			},
			textToMarkdown(text) {
				return this.convertBasedOnTags(
					text,
					tag => tag.tagMarkdown,
					tag => tag.textPosition,
					tag => tag.textLength
				);
			},
			textToHtml(text) {
				return this.convertBasedOnTags(
					text,
					tag => tag.tagHtml,
					tag => tag.textPosition,
					tag => tag.textLength
				);
			},
			htmlToText(html) {
				return this.convertBasedOnTags(
					html,
					tag => tag.tagText,
					tag => tag.htmlPosition,
					tag => tag.htmlLength
				);
			},
			htmlToMarkdown(html) {
				return this.convertBasedOnTags(
					html,
					tag => tag.tagMarkdown,
					tag => tag.htmlPosition,
					tag => tag.htmlLength
				);
			},
			convertBasedOnTags(startContent, replacementSelector, positionSelector, lengthSelector) {
				let lastTag;
				let threshold = Infinity;

				let newContent = _.cloneDeep(startContent);

				while ((lastTag = this.searchLastTagBelow(positionSelector, threshold)) != false) {
					let lastPosition = positionSelector(lastTag);
					let length = lengthSelector(lastTag);
					threshold = lastPosition;

					newContent = newContent.substr(0, lastPosition) + replacementSelector(lastTag) + newContent.substr(lastPosition + length);
				}

				return newContent;
			},
			searchLastTagBelow(positionSelector, threshold) {
				let position = 0;
				let tag = false;

				for (var i = this.tags.length - 1; i >= 0; i--) {
					let currentTag = this.tags[i];
					let currentPos = positionSelector(currentTag);

					if (currentPos >= position && currentPos < threshold) {
						position = currentPos;
						tag = currentTag;
					}
				}

				return tag;
			},
			switchToPreview() {
				this.suggestions = "";
				this.markdownData = this.textToMarkdown(document.getElementById('edit-box').innerText);
				this.outOfSyncInputData = this.initTagData(this.markdownData);
				this.preview = true;
			},

			initTagData(content) {
				let tagRegex = new RegExp("@([A-Za-z-'\. ]+):([A-Za-z0-9-\.]{4,40});");

				this.tags = [];

				let innerText = _.cloneDeep(content);
				let htmlOffset = 0;
				let found;
				while ((found = tagRegex.exec(innerText)) != null) {
					// if (! this.isTaggable(found[2])) {
					// 	continue;
					// }
					// ^ This results in an infinite loop because the global flag is not set.

					let tagHtml = `<span contenteditable="false" data-username="${found[2]}" class="bg-gray-200 px-1 rounded inline-block">@${found[1]}</span>`;

					this.tags.push({
						textPosition: found.index,
						htmlPosition: found.index + htmlOffset,
						textLength: found[1].length + 1,
						htmlLength: tagHtml.length,
						tagText: "@" + found[1],
						tagMarkdown: found[0],
						tagHtml: tagHtml,
						username: found[2],
					});

					htmlOffset += tagHtml.length - (found[1].length + 1);
					innerText = innerText.substr(0, found.index) + "@" + found[1] + innerText.substr(found.index + found[0].length);
				}

				this.contentLength = innerText.length;

				// Set global flag
				tagRegex = tagRegex.constructor(tagRegex, "g");
				return content.replace(tagRegex, '<span contenteditable="false" data-username="$2" class="bg-gray-200 px-1 rounded inline-block">@$1</span>');
			},
			// isTaggable(username) {
			// 	for (var i = this.tagSuggestions.length - 1; i >= 0; i--) {
			// 		if (this.tagSuggestions[i].username == username) {
			// 			return true;
			// 		}
			// 	}

			// 	return false;
			// },
			onClick(e) {
				this.tryToMatch(e.srcElement);
			},
			onKeyUp(e) {
				this.tryToMatch(e.srcElement);
			},
			tryToMatch(srcElement) {
				this.updateTagPositions(srcElement.innerText.length, this.cursorPosition);
				this.removeDeletedTags(srcElement);

				this.$emit("update:content", this.markdownData);

				this.updateCursorPosition(srcElement);
				this.mapAtSymbols(srcElement.innerText);
				this.findMatch(srcElement.innerText);
			},
			removeDeletedTags(srcElement) {
				let text = srcElement.innerText;

				for (var i = this.tags.length - 1; i >= 0; i--) {
					let tag = this.tags[i];

					if (! text.substr(tag.textPosition).startsWith(tag.tagText)) {
						this.tags.splice(i, 1);
						this.markdownData = this.textToMarkdown(text);
						this.htmlData = this.textToHtml(text);
						this.outOfSyncInputData = this.htmlData;
						let scroll = srcElement.scrollTop;
						setCaretPosition(srcElement, this.cursorPosition + 17);
						srcElement.scrollTop = scroll;
					}
				}
			},
			updateCursorPosition(srcElement) {
				let newCursorPosition = getCaretPosition(srcElement);

				if (typeof newCursorPosition === "undefined" || newCursorPosition < 0) {
					this.cursorPosition = 0;
					this.focus = false;
				}

				this.focus = true;
				this.cursorPosition = newCursorPosition;
			},
			updateTagPositions(newLength, cursorPosition) {
				if (newLength == this.contentLength) {
					return;
				}

				let diff = newLength - this.contentLength;

				for (var i = this.tags.length - 1; i >= 0; i--) {
					let tag = this.tags[i];

					if (tag.textPosition > cursorPosition) {
						tag.textPosition += diff;
						tag.htmlPosition += diff;
					}
				}

				this.contentLength = newLength;
			},
			mapAtSymbols(text) {
				this.atSymbols = [];
				let regex = /@/g;

				let found;
				while ((found = regex.exec(text)) != null) {
					this.atSymbols.push({
						position: found.index,
					});
				}
			},
			findMatch(text) {
				let closestAt = this.findClosestAtSymbol();

				if (closestAt < 0 || this.cursorPosition < 0) {
					return false;
				}

				let substr = text.substr(closestAt, this.cursorPosition - closestAt);

				if (! this.isTag(closestAt)) {
					this.matchAndSuggest(substr, closestAt);
				}
			},
			isTag(tagPosition) {
				for (var i = this.tags.length - 1; i >= 0; i--) {
					if (this.tags[i].textPosition == tagPosition) {
						return true;
					}
				}

				return false;
			},
			findClosestAtSymbol() {
				let distance = Infinity;
				let closest = {position: -1};

				for (var i = this.atSymbols.length - 1; i >= 0; i--) {
					let atPosition = this.atSymbols[i].position;
					let atDistance = this.cursorPosition - atPosition;

					if (atDistance > 0 && atDistance < distance) {
						distance = atDistance;
						closest = this.atSymbols[i];
					}
				}

				return closest.position;
			},
			matchAndSuggest(substr, atPosition) {
				let matches = [];

				for (var i = this.tagSuggestions.length - 1; i >= 0; i--) {
					let suggestion = this.tagSuggestions[i];
					let length;

					if ((length = this.matchesInSomeWay(suggestion, substr)) > 1) {
						matches.push({
							startIndex: atPosition,
							currentIndex: this.cursorPosition,
							length: length,

							username: suggestion.username,
							firstName: suggestion.first_name,
							name: suggestion.first_name + " " + ((suggestion.middle_name == null) ? "" : suggestion.middle_name + " ") + suggestion.last_name,
						});
					}
				}

				this.suggestions = "";

				if (matches.length) {
					this.showMatchResults(matches);
				}
			},
			matchesInSomeWay(suggestion, string) {
				let matchPart = string.substr(1).toLocaleLowerCase(); // Remove @ symbol
				let name = suggestion.first_name + " " + ((suggestion.middle_name == null) ? "" : suggestion.middle_name + " ") + suggestion.last_name;

				if (name.toLocaleLowerCase().startsWith(matchPart)
					|| suggestion.last_name.toLocaleLowerCase().startsWith(matchPart)) {
					return matchPart.length;
				}

				return 0;
			},
			showMatchResults(matches) {
				for (var i = matches.length - 1; i >= 0; i--) {
					let match = matches[i];

					this.suggestions += `<span 	data-name="${match.firstName}"
												data-username="${match.username}"
												data-atposition="${match.startIndex}"
												data-caretposition="${match.currentIndex}"
												data-length="${match.length}"
												class="align-middle block fixed hover:bg-gray-200 cursor-pointer px-2 py-1 rounded">@${match.firstName}</span>
										<span 	data-name="${match.name}"
												data-username="${match.username}"
												data-atposition="${match.startIndex}"
												data-caretposition="${match.currentIndex}"
												data-length="${match.length}"
												class="block hover:bg-gray-200 cursor-pointer px-2 py-1 rounded">@${match.name}</span>`;
				}

			},
			addTag(e) {
				let name     = e.srcElement.dataset.name;
				let username = e.srcElement.dataset.username;
				let length   = parseInt(e.srcElement.dataset.length);
				let atpos    = parseInt(e.srcElement.dataset.atposition);
				let caretpos = parseInt(e.srcElement.dataset.caretposition);

				if (typeof name === "undefined" || typeof username === "undefined") {
					return;
				}

				let container = document.getElementById("edit-box");
				let scrollPos = container.scrollTop;

				this.replaceTagInData(container, atpos, caretpos, `@${name}:${username};`);
				setCaretPosition(container, caretpos + name.length - length + 23);
				container.scrollTop = scrollPos;

				this.focus = false;
				this.suggestions = "";
			},
			replaceTagInData(container, atpos, caretpos, tag) {
				let text = container.innerText;

				let updatedTextData = text.substr(0, atpos) + tag + " " + text.substr(caretpos);
				this.updateTagPositions(updatedTextData.length, atpos);

				this.markdownData = this.textToMarkdown(updatedTextData);
				this.htmlData = this.markdownToHtml(this.markdownData);
				this.outOfSyncInputData = this.initTagData(this.markdownData);
			},
		}
	}

	// https://stackoverflow.com/a/41034697/2993212
	function createRange(node, chars, range) {
	    if (!range) {
	        range = document.createRange()
	        range.selectNode(node);
	        range.setStart(node, 0);
	    }

	    if (chars.count === 0) {
	        range.setEnd(node, chars.count);
	    } else if (node && chars.count >0) {
	        if (node.nodeType === Node.TEXT_NODE) {
	            if (node.textContent.length < chars.count) {
	                chars.count -= node.textContent.length;
	            } else {
	                 range.setEnd(node, chars.count);
	                 chars.count = 0;
	            }
	        } else {
	            for (var lp = 0; lp < node.childNodes.length; lp++) {
	                range = createRange(node.childNodes[lp], chars, range);

	                if (chars.count === 0) {
	                   break;
	                }
	            }
	        }
	   }

	   return range;
	};

	function setCaretPosition(el, chars) {
	    if (chars >= 0) {
	    	el.focus();
	    	setTimeout(function() {
		        var selection = window.getSelection();

		        var range = createRange(el.parentNode, { count: chars });

		        if (range) {
		            range.collapse(false);
		            selection.removeAllRanges();
		            selection.addRange(range);
		        }
		    }, 100);
	    }
	};


	// Adapted from https://stackoverflow.com/a/53128599/2993212
	// node_walk: walk the element tree, stop when func(node) returns false
	function node_walk(node, func) {
		var result = func(node);
		for(node = node.firstChild; result !== false && node; node = node.nextSibling)
			result = node_walk(node, func);
		return result;
	};

	// getCaretPosition: return [start, end] as offsets to elem.textContent that
	//   correspond to the selected portion of text
	//   (if start == end, caret is at given position and no text is selected)
	function getCaretPosition(elem) {
		var sel = window.getSelection();
		var cum_length = [0, 0];

		if(sel.anchorNode == elem)
			cum_length = [sel.anchorOffset, sel.extentOffset];
		else {
			var nodes_to_find = [sel.anchorNode, sel.extentNode];
			if(!elem.contains(sel.anchorNode) || !elem.contains(sel.extentNode))
				return -1;
			else {
				var found = [0,0];
				var i;
				node_walk(elem, function(node) {
					for(i = 0; i < 2; i++) {
						if(node == nodes_to_find[i]) {
							found[i] = true;
							if(found[i == 0 ? 1 : 0])
								return false; // all done
						}
					}

					if(node.textContent && !node.firstChild) {
						for(i = 0; i < 2; i++) {
							if(!found[i])
								cum_length[i] += node.textContent.length;
						}
					}
				});
				cum_length[0] += sel.anchorOffset;
				cum_length[1] += sel.extentOffset;
			}
		}

		return cum_length[0];
	}
</script>
