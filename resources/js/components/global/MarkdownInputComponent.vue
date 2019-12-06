<template>
	<div>
		<div v-if="suggestions.length" class="fixed top-0 h-full left-0 ml-24 flex flex-col justify-center">
			<div @click.prevent="addTag" class="bg-gray-100 h-9 px-2 py-2 rounded shadow sticky text-left text-xl w-56" v-html="suggestions"></div>
		</div>
		<label class="text-gray-700 mt-3 block" for="content">{{ label }}</label>
		<div class="mt-2">
			<a :class="{'font-bold': !preview}" class="bg-gray-100 inline-block mr-1 px-8 py-4 rounded shadow hover:shadow-md" @click.prevent="preview = false" href="#">Edit</a>
			<a :class="{'font-bold': preview}" class="bg-gray-100 inline-block px-8 py-4 rounded shadow hover:shadow-md" @click.prevent="outOfSyncInputData = initTagData(data);preview = true" href="#">Preview</a>
		</div>
		<div v-if="!preview" @focus="suggestions = '';focus = true" @blur="focus = false" @click="onClick" @keyup="onKeyUp" @input="onEditContent" id="edit-box" contenteditable="true" class="inline-block whitespace-pre-wrap leading-tight resize-y w-full h-64 overflow-y-auto bg-gray-100 block mt-2 px-4 py-3 shadow rounded focus:outline-none focus:shadow-md" v-html="outOfSyncInputData"></div>
		<div v-else>
			<MarkdownOutputComponent :content="data"></MarkdownOutputComponent>
		</div>
		<div>
			<span></span>
			<span></span>
		</div>
	</div>
</template>

<script>
	// @ positions in map
	// every move of caret check where closest @ before caret is and substring + match
	//
	// Only parse tags available in prop
	//
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
				markdownData: this.content,
				htmlData: this.content,
				outOfSyncInputData: this.content,
				preview: false,
				suggestions: "",

				//

				cursorPosition: 0,
				contentLength: 0,
				focus: false,
				atSymbols: [], // Parsed on every insert
				tags: [], // Parsed at the beginning when loading data, positions updated on every insert
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
			};
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
			// START THIRD TRY, FOR REAL NOW
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

			},
			textToHtml(text) {

			},
			htmlToText(html) {

			},
			htmlToMarkdown(html) {

			},
			convertBasedOnTags(startContent, positionSelector) {

			},

			///////////////////
			// UPDATING TAGS //
			///////////////////

			// START NEW TRY
			initTagData(content) {
				// console.log("INIT\n", content);
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

				this.updateCursorPosition(srcElement);
				// console.log(this.cursorPosition);
				this.mapAtSymbols(srcElement.innerText);
				this.findMatch(srcElement.innerText);
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
			onEditContent(e) {
				// console.log("edit", e);
			},
			updateTagPositions(newLength, cursorPosition) {
				if (newLength == this.contentLength) {
					return;
				}

				let diff = newLength - this.contentLength;

				for (var i = this.tags.length - 1; i >= 0; i--) {
					let tag = this.tags[i];

					if (tag.position > cursorPosition) {
						tag.position += diff;
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

				// console.log(this.atSymbols);
			},
			findMatch(text) {
				let closestAt = this.findClosestAtSymbol();

				if (closestAt.position < 0 || this.cursorPosition < 0) {
					return false;
				}

				let substr = text.substr(closestAt, this.cursorPosition - closestAt);

				if (! this.isTag(closestAt)) {
					this.matchAndSuggest(substr, closestAt.position);
				}
			},
			isTag(tagPosition) {
				for (var i = this.tags.length - 1; i >= 0; i--) {
					if (this.tags[i].position == tagPosition) {
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
				// For each in prop
				// Try to match first_name, last_name and middle name + concatenation exactly.
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
				console.log(e.srcElement);
				let name     = e.srcElement.dataset.name;
				let username = e.srcElement.dataset.username;
				let length   = parseInt(e.srcElement.dataset.length);
				let atpos    = parseInt(e.srcElement.dataset.atposition);
				let caretpos = parseInt(e.srcElement.dataset.caretposition);

				if (typeof name === "undefined" || typeof username === "undefined") {
					return;
				}

				// Add tag in this.tags
				// Update other tags (name.length - length difference) (they move back)
				// replace tag in data
				// update cursor position

				// this.outOfSyncInputData = this.raw;
				let container = document.getElementById("edit-box");

				this.replaceTagInData(container, atpos, caretpos, `@${name}:${username};`);
				setCaretPosition(container, caretpos + name.length - length);

				// this.suggestions = "";
			},
			replaceTagInData(container, atpos, caretpos, tag) {
				let text = container.innerText;

				this.markdownData = text.substr(0, atpos) + tag + text.substr(caretpos);
				this.outOfSyncInputData = this.initTagData(this.markdownData);
			},
			// END NEW TRY

			// addTag(e) {
			// 	let name = e.srcElement.dataset.name;
			// 	let username = e.srcElement.dataset.username;
			// 	let length = parseInt(e.srcElement.dataset.length);
			// 	let caretpos = parseInt(e.srcElement.dataset.caretposition);

			// 	if (typeof name === "undefined" || typeof username === "undefined") {
			// 		return;
			// 	}

			// 	this.outOfSyncInputData = this.raw;
			// 	let container = document.getElementById("edit-box");

			// 	this.replaceTagInData(container, caretpos, `@${name}:${username};`);
			// 	setCaretPosition(container, caretpos + name.length + 21);

			// 	this.suggestions = "";
			// },
			// replaceTagInData(container, caretpos, tag) {
			// 	let subdata = container.innerText.substr(0, caretpos);
			// 	let at = subdata.lastIndexOf("@");

			// 	let exactContent = container.innerText.substr(at, caretpos - at);
			// 	let regex = new RegExp("(?<!>)(" + exactContent + ")(?!<)", "g");

			// 	this.raw = container.innerHTML.replace(regex, this.replaceMarkdownByTags(tag + " "));
			// 	this.outOfSyncInputData = this.raw;
			// 	this.updateData({target: {innerHTML: this.raw}});
			// },
			// updateData(e) {
			// 	console.log(e);
			// 	var innerdata = e.target.innerHTML;

			// 	if (! this.checkForSuggestions(innerdata, e.target)) {
			// 		this.suggestions = "";
			// 	}

			// 	this.raw = e.target.innerHTML;
			// 	this.markdownData = this.replaceTagsByMarkdown(this.raw);
			// 	this.$emit('update:content', this.markdownData);
			// },
			// replaceMarkdownByTags(data) {
			// 	let regex = /@([A-Za-z-'\. ]+):([A-Za-z0-9-\.]{4,40});/g;

			// 	return data.replace(regex, '<span contenteditable="false" data-username="$2" class="bg-gray-200 px-1 rounded inline-block">@$1</span>');
			// },
			// checkForSuggestions(data, container) {
			// 	let regex = /(?<!>)@([A-Za-z\.' ]+)(?!<)/g;

			// 	let found;
			// 	while ((found = regex.exec(data)) != null) {
			// 		if (this.matchAndSuggest(found[1], container)) {
			// 			return true;
			// 		}
			// 	}

			// 	return false;
			// },
			// matchAndSuggest(match, container) {
			// 	let match_parts = match.split(" ");

			// 	if (match_parts.length == 0) {
			// 		return false;
			// 	}

			// 	if (match_parts[0].length <= 1) {
			// 		return false;
			// 	}

			// 	let matches = {};
			// 	let current_part_index = 0;
			// 	let current_part = "";
			// 	let max_match_index = 0;

			// 	while (current_part_index < match_parts.length) {
			// 		current_part += match_parts[current_part_index];

			// 		for (var i = this.tagSuggestions.length - 1; i >= 0; i--) {
			// 			let s = this.tagSuggestions[i];
			// 			let name = s.first_name + " " + ((s.middle_name === null) ? "" : s.middle_name + " ") + s.last_name;

			// 			let index = name.toLocaleLowerCase().indexOf(current_part.toLocaleLowerCase());

			// 			if (index >= 0) {
			// 				max_match_index = current_part_index;

			// 				matches[s.username] = {
			// 					index: index,
			// 					length: current_part.length,
			// 					short: s.first_name,
			// 					name: name,
			// 					username: s.username,
			// 				};
			// 			}
			// 		}

			// 		if (max_match_index !== current_part_index) {
			// 			break;
			// 		}

			// 		current_part += " ";
			// 		current_part_index++;
			// 	}

			// 	if (Object.keys(matches).length === 0) {
			// 		return false;
			// 	}

			// 	this.showMatchResults(matches, container);
			// 	return true;
			// },
			// showMatchResults(matches, container) {
			// 	let max = 0;
			// 	let match = null;
			// 	let caretPosition = getCaretPosition(container)[0];

			// 	for (var i = Object.values(matches).length - 1; i >= 0; i--) {
			// 		let current = Object.values(matches)[i];

			// 		if (current.length > max) {
			// 			max = current.length;
			// 			match = current;
			// 		}
			// 	}

			// 	this.suggestions = `<span data-name="${match.short}" data-username="${match.username}" data-caretposition="${caretPosition}" data-length="${match.length}" class="align-middle block fixed hover:bg-gray-200 cursor-pointer px-2 py-1 rounded">@${match.short}</span><span data-name="${match.name}" data-username="${match.username}" data-caretposition="${caretPosition}" data-length="${match.length}" class="block hover:bg-gray-200 cursor-pointer px-2 py-1 rounded">@${match.name}</span>`;
			// },
			// replaceTagsByMarkdown(data) {
			// 	let regex = /<[a-z=" ]+data-username="([A-Za-z0-9-\.]{4,40})"[a-z0-9-=" ]+>@([A-Za-z-'\. ]+)<\/span>/g;

			// 	return data.replace(regex, '@$2:$1;');
			// }
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
