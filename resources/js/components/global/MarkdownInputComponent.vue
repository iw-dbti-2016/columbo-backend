<template>
	<div>
		<div v-if="suggestions.length" class="fixed top-0 h-full left-0 ml-24 flex flex-col justify-center">
			<div @click.prevent="addTag" class="bg-gray-100 h-9 px-2 py-2 rounded shadow sticky text-left text-xl w-56" v-html="suggestions"></div>
		</div>
		<label class="text-gray-700 mt-3 block" for="content">{{ label }}</label>
		<div class="mt-2">
			<a :class="{'font-bold': !preview}" class="bg-gray-100 inline-block mr-1 px-8 py-4 rounded shadow hover:shadow-md" @click.prevent="preview = false" href="#">Edit</a>
			<a :class="{'font-bold': preview}" class="bg-gray-100 inline-block px-8 py-4 rounded shadow hover:shadow-md" @click.prevent="data2 = replaceMarkdownByTags(data);preview = true" href="#">Preview</a>
		</div>
		<div v-if="!preview" @input="updateData" id="edit-box" contenteditable="true" class="inline-block whitespace-pre-wrap leading-tight resize-y w-full h-64 overflow-y-auto bg-gray-100 block mt-2 px-4 py-3 shadow rounded focus:outline-none focus:shadow-md" v-html="replaceMarkdownByTags(data2)"></div>
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
					];
				},
			},
		},
		data() {
			return {
				data: this.content,
				data2: this.content,
				raw: this.content,
				preview: false,
				suggestions: "",
			};
		},
		watch: {
			content: function(value) {
				this.data = value;

				if (this.data2.length === 0) {
					this.data2 = value;
					this.raw = value;
				}
			}
		},
		methods: {
			addTag(e) {
				let name = e.srcElement.dataset.name;
				let username = e.srcElement.dataset.username;
				let length = parseInt(e.srcElement.dataset.length);
				let caretpos = parseInt(e.srcElement.dataset.caretposition);

				if (typeof name === "undefined" || typeof username === "undefined") {
					return;
				}

				this.data2 = this.raw;
				let container = document.getElementById("edit-box");

				this.replaceTagInData(container, caretpos, `@${name}:${username};`);
				setCaretPosition(container, caretpos + name.length + 21);

				this.suggestions = "";
			},
			replaceTagInData(container, caretpos, tag) {
				let subdata = container.innerText.substr(0, caretpos);
				let at = subdata.lastIndexOf("@");

				let exactContent = container.innerText.substr(at, caretpos - at);
				let regex = new RegExp("(?<!>)(" + exactContent + ")(?!<)", "g");

				this.raw = container.innerHTML.replace(regex, this.replaceMarkdownByTags(tag));
				this.data2 = this.raw;
				this.updateData({target: {innerHTML: this.raw}});
			},
			updateData(e) {
				var innerdata = e.target.innerHTML;

				if (! this.checkForSuggestions(innerdata, e.target)) {
					this.suggestions = "";
				}

				this.raw = e.target.innerHTML;
				this.data = this.replaceTagsByMarkdown(this.raw);
				this.$emit('update:content', this.data);
			},
			replaceMarkdownByTags(data) {
				let regex = /@([A-Za-z-'\. ]+):([A-Za-z0-9-\.]{4,40});/g;

				return data.replace(regex, '<span contenteditable="false" data-username="$2" class="bg-gray-200 px-1 rounded inline-block">@$1</span> ');
			},
			checkForSuggestions(data, container) {
				let regex = /(?<!>)@([A-Za-z\.' ]+)(?!<)/g;

				let found;
				while ((found = regex.exec(data)) != null) {
					if (this.matchAndSuggest(found[1], container)) {
						return true;
					}
				}

				return false;
			},
			matchAndSuggest(match, container) {
				let match_parts = match.split(" ");

				if (match_parts.length == 0) {
					return false;
				}

				if (match_parts[0].length <= 1) {
					return false;
				}

				let matches = {};
				let current_part_index = 0;
				let current_part = "";
				let max_match_index = 0;

				while (current_part_index < match_parts.length) {
					current_part += match_parts[current_part_index];

					for (var i = this.tagSuggestions.length - 1; i >= 0; i--) {
						let s = this.tagSuggestions[i];
						let name = s.first_name + " " + ((s.middle_name === null) ? "" : s.middle_name + " ") + s.last_name;

						let index = name.toLocaleLowerCase().indexOf(current_part.toLocaleLowerCase());

						if (index >= 0) {
							max_match_index = current_part_index;

							matches[s.username] = {
								index: index,
								length: current_part.length,
								short: s.first_name,
								name: name,
								username: s.username,
							};
						}
					}

					if (max_match_index !== current_part_index) {
						break;
					}

					current_part += " ";
					current_part_index++;
				}

				if (Object.keys(matches).length === 0) {
					return false;
				}

				this.showMatchResults(matches, container);
				return true;
			},
			showMatchResults(matches, container) {
				let max = 0;
				let match = null;
				let caretPosition = getCaretPosition(container)[0];

				for (var i = Object.values(matches).length - 1; i >= 0; i--) {
					let current = Object.values(matches)[i];

					if (current.length > max) {
						max = current.length;
						match = current;
					}
				}

				this.suggestions = `<span data-name="${match.short}" data-username="${match.username}" data-caretposition="${caretPosition}" data-length="${match.length}" class="align-middle block fixed hover:bg-gray-200 cursor-pointer px-2 py-1 rounded">@${match.short}</span><span data-name="${match.name}" data-username="${match.username}" data-caretposition="${caretPosition}" data-length="${match.length}" class="block hover:bg-gray-200 cursor-pointer px-2 py-1 rounded">@${match.name}</span>`;
			},
			replaceTagsByMarkdown(data) {
				let regex = /<[a-z=" ]+data-username="([A-Za-z0-9-\.]{4,40})"[a-z0-9-=" ]+>@([A-Za-z-'\. ]+)<\/span>/g;

				return data.replace(regex, '@$2:$1;');
			}
		}
	}


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


	// https://stackoverflow.com/a/53128599/2993212
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
				return undefined;
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
		if(cum_length[0] <= cum_length[1])
			return cum_length;
		return [cum_length[1], cum_length[0]];
	}
</script>
