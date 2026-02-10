<template>
    <default-field
        :field="currentField"
        :errors="errors"
        :full-width-content="true"
        :show-help-text="showHelpText"
    >
        <template #field>
            <div style="position: relative; top: 0; left: 0">
                <div
                    v-show="!currentField.readonly"
                    class="tiptap-toolbar"
                    style="z-index: 10; position: sticky; top: 0; left: 0"
                >
                    <div class="tiptap-toolbar__inner">
                        <div
                            v-for="button in buttons"
                            :key="'button-' + button"
                            :class="{
                                'inline-block': button != 'br',
                            }"
                        >
                            <template v-if="button == '|'">
                                <div class="tiptap-separator"></div>
                            </template>

                            <template v-else-if="button == 'br'"> </template>

                            <template v-else-if="button == 'heading' || button == 'headingDropdown'">
                                <heading-dropdown
                                    :headingLevels="headingLevels"
                                    :mode="mode"
                                    :editor="editor"
                                >
                                </heading-dropdown>
                            </template>

                            <template v-else-if="button == 'listDropdown'">
                                <list-dropdown
                                    :mode="mode"
                                    :editor="editor"
                                >
                                </list-dropdown>
                            </template>

                            <template v-else-if="button == 'link'">
                                <link-button
                                    :editor="editor"
                                    :button="button"
                                    :field="currentField"
                                    :mode="mode"
                                    :fileDisk="fileDisk"
                                    :filePath="filePath"
                                >
                                </link-button>
                            </template>

                            <template v-else-if="button == 'image'">
                                <image-button
                                    :editor="editor"
                                    :button="button"
                                    :field="currentField"
                                    :mode="mode"
                                    :imageDisk="imageDisk"
                                    :imagePath="imagePath"
                                >
                                </image-button>
                            </template>

                            <template v-else-if="button == 'placeholderBlock'">
                                <placeholder-block-button
                                    :editor="editor"
                                    :button="button"
                                    :field="currentField"
                                    :mode="mode"
                                >
                                </placeholder-block-button>
                            </template>

                            <template v-else-if="button == 'contentBlock'">
                                <content-block-button
                                    :editor="editor"
                                    :button="button"
                                    :field="currentField"
                                    :mode="mode"
                                    :imageDisk="imageDisk"
                                    :imagePath="imagePath"
                                >
                                </content-block-button>
                            </template>

                            <template v-else-if="button == 'textAlign'">
                                <text-align-buttons
                                    :editor="editor"
                                    :mode="mode"
                                    :alignments="alignments"
                                    :alignElements="alignElements"
                                    :defaultAlignment="defaultAlignment"
                                >
                                </text-align-buttons>
                            </template>

                            <template v-else-if="button == 'alignDropdown'">
                                <align-dropdown
                                    :editor="editor"
                                    :mode="mode"
                                    :alignments="alignments"
                                    :alignElements="alignElements"
                                    :defaultAlignment="defaultAlignment"
                                >
                                </align-dropdown>
                            </template>

                            <template v-else-if="button == 'rtl'">
                                <rtl-button :editor="editor" :mode="mode">
                                </rtl-button>
                            </template>

                            <template v-else-if="button == 'history'">
                                <history-buttons :editor="editor" :mode="mode">
                                </history-buttons>
                            </template>

                            <template v-else-if="button == 'editHtml'">
                                <base-button
                                    :isActive="mode == 'html'"
                                    :clickMethod="switchMode"
                                    :icon="['fas', 'file-code']"
                                    :title="__('edit html')"
                                >
                                </base-button>
                            </template>

                            <template v-else-if="button == 'color'">
                                <color-button
                                    :editor="editor"
                                    :colors="colors"
                                    mode="color"
                                >
                                </color-button>
                            </template>

                            <template v-else-if="button == 'backgroundColor'">
                                <color-button
                                    :editor="editor"
                                    :colors="backgroundColors"
                                    mode="backgroundColor"
                                >
                                </color-button>
                            </template>

                            <template v-else-if="button == 'tableAlternative'">
                                <table-button
                                    :editor="editor"
                                    :table-cell-background-colors="
                                        tableCellBackgroundColors
                                    "
                                    :table-cell-border-colors="
                                        tableCellBorderColors
                                    "
                                ></table-button>
                            </template>

                            <template v-else>
                                <normal-button
                                    :editor="editor"
                                    :button="button"
                                    :mode="mode"
                                >
                                </normal-button>
                            </template>
                        </div>
                    </div>

                    <div
                        class="flex items-center rounded"
                        style="z-index: 10"
                        v-if="tableIsActive"
                    >
                        <table-buttons :editor="editor"> </table-buttons>
                    </div>
                </div>

                <div
                    class="w-full nova-tiptap-editor tiptap-editor-content"
                    :style="cssProps"
                    v-show="mode == 'editor'"
                >
                    <editor-content :editor="editor" />

                    <bubble-menu
                        v-if="editor && showBubbleMenu"
                        :editor="editor"
                        :tippy-options="{ duration: 100 }"
                        class="tiptap-bubble-menu"
                    >
                        <template v-for="button in bubbleMenuButtons" :key="'bubble-' + button">
                            <!-- Bold -->
                            <button
                                v-if="button === 'bold'"
                                type="button"
                                @click="editor.chain().focus().toggleBold().run()"
                                :class="{ 'is-active': editor.isActive('bold') }"
                                :title="__('bold')"
                            >
                                <tiptap-icon name="bold" />
                            </button>

                            <!-- Italic -->
                            <button
                                v-if="button === 'italic'"
                                type="button"
                                @click="editor.chain().focus().toggleItalic().run()"
                                :class="{ 'is-active': editor.isActive('italic') }"
                                :title="__('italic')"
                            >
                                <tiptap-icon name="italic" />
                            </button>

                            <!-- Strike -->
                            <button
                                v-if="button === 'strike'"
                                type="button"
                                @click="editor.chain().focus().toggleStrike().run()"
                                :class="{ 'is-active': editor.isActive('strike') }"
                                :title="__('strikethrough')"
                            >
                                <tiptap-icon name="strike" />
                            </button>

                            <!-- Underline -->
                            <button
                                v-if="button === 'underline'"
                                type="button"
                                @click="editor.chain().focus().toggleUnderline().run()"
                                :class="{ 'is-active': editor.isActive('underline') }"
                                :title="__('underline')"
                            >
                                <tiptap-icon name="underline" />
                            </button>

                            <!-- Code -->
                            <button
                                v-if="button === 'code'"
                                type="button"
                                @click="editor.chain().focus().toggleCode().run()"
                                :class="{ 'is-active': editor.isActive('code') }"
                                :title="__('code')"
                            >
                                <tiptap-icon name="code" />
                            </button>

                            <!-- Highlight -->
                            <button
                                v-if="button === 'highlight'"
                                type="button"
                                @click="editor.chain().focus().toggleHighlight().run()"
                                :class="{ 'is-active': editor.isActive('highlight') }"
                                :title="__('highlight')"
                            >
                                <tiptap-icon name="highlight" />
                            </button>

                            <!-- Subscript -->
                            <button
                                v-if="button === 'subscript'"
                                type="button"
                                @click="editor.chain().focus().toggleSubscript().run()"
                                :class="{ 'is-active': editor.isActive('subscript') }"
                                :title="__('subscript')"
                            >
                                <tiptap-icon name="subscript" />
                            </button>

                            <!-- Superscript -->
                            <button
                                v-if="button === 'superscript'"
                                type="button"
                                @click="editor.chain().focus().toggleSuperscript().run()"
                                :class="{ 'is-active': editor.isActive('superscript') }"
                                :title="__('superscript')"
                            >
                                <tiptap-icon name="superscript" />
                            </button>

                            <!-- Link -->
                            <div v-if="button === 'link'" class="tiptap-bubble-menu__dropdown">
                                <button
                                    type="button"
                                    @click="toggleBubbleLink"
                                    :class="{ 'is-active': editor.isActive('link') }"
                                    :title="__('link')"
                                >
                                    <tiptap-icon name="link" />
                                </button>
                                <div v-show="bubbleLinkOpen" class="tiptap-bubble-menu__dropdown-panel">
                                    <input
                                        v-model="bubbleLinkUrl"
                                        type="text"
                                        class="tiptap-bubble-menu__input"
                                        :placeholder="__('Paste a link...')"
                                        @keydown.enter.prevent="applyBubbleLink"
                                    />
                                    <button type="button" class="tiptap-bubble-menu__dropdown-btn" @click="applyBubbleLink" :disabled="!bubbleLinkUrl">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                    <button v-if="editor.isActive('link')" type="button" class="tiptap-bubble-menu__dropdown-btn tiptap-bubble-menu__dropdown-btn--danger" @click="removeBubbleLink">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Text Color -->
                            <div v-if="button === 'color'" class="tiptap-bubble-menu__dropdown">
                                <button
                                    type="button"
                                    @click="toggleBubbleColor"
                                    :class="{ 'is-active': editor.isActive('textStyle', { color: /./ }) }"
                                    :title="__('text color')"
                                >
                                    <tiptap-icon name="color" />
                                </button>
                                <div v-show="bubbleColorOpen" class="tiptap-bubble-menu__color-panel">
                                    <button
                                        v-for="color in colors"
                                        :key="'bubble-color-' + color"
                                        type="button"
                                        class="tiptap-bubble-menu__color-swatch"
                                        :style="{ backgroundColor: color }"
                                        @click="setBubbleColor(color)"
                                        :title="color"
                                    ></button>
                                    <button
                                        type="button"
                                        class="tiptap-bubble-menu__color-reset"
                                        @click="unsetBubbleColor"
                                        :title="__('remove color')"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Background Color -->
                            <div v-if="button === 'backgroundColor'" class="tiptap-bubble-menu__dropdown">
                                <button
                                    type="button"
                                    @click="toggleBubbleBgColor"
                                    :class="{ 'is-active': editor.isActive('backgroundColor', { backgroundColor: /./ }) }"
                                    :title="__('background color')"
                                >
                                    <tiptap-icon name="backgroundColor" />
                                </button>
                                <div v-show="bubbleBgColorOpen" class="tiptap-bubble-menu__color-panel">
                                    <button
                                        v-for="color in backgroundColors"
                                        :key="'bubble-bgcolor-' + color"
                                        type="button"
                                        class="tiptap-bubble-menu__color-swatch"
                                        :style="{ backgroundColor: color }"
                                        @click="setBubbleBgColor(color)"
                                        :title="color"
                                    ></button>
                                    <button
                                        type="button"
                                        class="tiptap-bubble-menu__color-reset"
                                        @click="unsetBubbleBgColor"
                                        :title="__('remove color')"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Separator -->
                            <div v-if="button === '|'" class="tiptap-bubble-menu__separator"></div>
                        </template>
                    </bubble-menu>
                </div>

                <div class="w-full px-0 mt-3" v-show="mode == 'html'">
                    <edit-html :theme="htmlTheme" v-model="htmlModeValue" />
                </div>
            </div>
        </template>
    </default-field>
</template>

<script>
    import { Editor, EditorContent, VueNodeViewRenderer } from "@tiptap/vue-3";
    import { BubbleMenu } from "@tiptap/vue-3/menus";

    import Text from "@tiptap/extension-text";

    import Blockquote from "@tiptap/extension-blockquote";
    import Bold from "@tiptap/extension-bold";
    import Code from "@tiptap/extension-code";
    import CodeBlock from "@tiptap/extension-code-block";
    import CodeBlockLowlight from "@tiptap/extension-code-block-lowlight";
    import Highlight from "@tiptap/extension-highlight";
    import HorizontalRule from "@tiptap/extension-horizontal-rule";
    import Italic from "@tiptap/extension-italic";
    import Link from "@tiptap/extension-link";
    import Strike from "@tiptap/extension-strike";
    import Subscript from "@tiptap/extension-subscript";
    import Superscript from "@tiptap/extension-superscript";
    import { TextStyle } from "@tiptap/extension-text-style";
    import Underline from "@tiptap/extension-underline";
    import { Color } from "@tiptap/extension-color";

    import Heading from "@tiptap/extension-heading";
    import TextAlign from "@tiptap/extension-text-align";
    import Document from "@tiptap/extension-document";

    // TipTap v3 consolidated imports
    import { BulletList, OrderedList, ListItem } from "@tiptap/extension-list";
    import { Table, TableRow, TableHeader } from "@tiptap/extension-table";
    import { UndoRedo, Placeholder, Dropcursor, Gapcursor } from "@tiptap/extensions";
    import { FileHandler } from "@tiptap/extension-file-handler";

    // Custom TableCell extension
    import TableCell from "../extensions/TableCell";

    import Paragraph from "@tiptap/extension-paragraph";
    import HardBreak from "@tiptap/extension-hard-break";

    import ImageResize from "tiptap-extension-resize-image";
    import ImageUploadNode from "../extensions/ImageUploadNode";

    import LinkButton from "./buttons/LinkButton";
    import NormalButton from "./buttons/NormalButton";
    import HeadingDropdown from "./buttons/HeadingDropdown.vue";
    import ListDropdown from "./buttons/ListDropdown.vue";
    import AlignDropdown from "./buttons/AlignDropdown.vue";
    import TableButtons from "./buttons/TableButtons";
    import TextAlignButtons from "./buttons/TextAlignButtons";
    import RtlButton from "./buttons/RtlButton";
    import HistoryButtons from "./buttons/HistoryButtons";
    import ImageButton from "./buttons/ImageButton";
    import PlaceholderBlockButton from "./buttons/PlaceholderBlockButton";
    import ContentBlockButton from "./buttons/ContentBlockButton";
    import BaseButton from "./buttons/BaseButton.vue";
    import { TiptapIcon } from "./icons";
    import ColorButton from "./buttons/ColorButton.vue";
    import TableButton from "./buttons/TableButton.vue";

    import CodeBlockComponent from "./CodeBlockComponent";
    import EditHtml from "./EditHtml";

    import { common, createLowlight } from "lowlight";
    import pretty from "pretty";

    const lowlight = createLowlight(common);

    import buttonHovers from "../mixins/buttonHovers";
    import contentSanitizer from "../mixins/contentSanitizer";

    import { DependentFormField, HandlesValidationErrors } from "laravel-nova";

    import PlaceholderBlockExtension from "../extensions/PlaceholderBlockExtension.js";
    import VideoContentBlockExtension from "./content-blocks/VideoContentBlockExtension.js";
    import GalleryContentBlockExtension from "./content-blocks/GalleryContentBlockExtension.js";
    import BackgroundColorExtension from "../extensions/BackgroundColor.js";

    export default {
        mixins: [
            DependentFormField,
            HandlesValidationErrors,
            buttonHovers,
            contentSanitizer,
        ],

        props: ["resourceName", "resourceId", "field"],

        components: {
            TableButton,
            ColorButton,
            EditorContent,
            BubbleMenu,
            LinkButton,
            NormalButton,
            HeadingDropdown,
            ListDropdown,
            AlignDropdown,
            TableButtons,
            TextAlignButtons,
            RtlButton,
            HistoryButtons,
            ImageButton,
            PlaceholderBlockButton,
            ContentBlockButton,
            EditHtml,
            BaseButton,
            TiptapIcon,
        },

        data() {
            return {
                editor: null,
                mode: "editor",
                htmlModeValue: "",
                placeholder: "",
                imageDisk: "",
                imagePath: "",
                fileDisk: "",
                filePath: "",
                initialFieldValue: null,
                defaultColors: [
                    "#000000",
                    "#ff133b",
                    "#0000ff",
                    "#008000",
                    "#ffff00",
                    "#ffa500",
                ],
                // Bubble menu state
                bubbleLinkOpen: false,
                bubbleLinkUrl: "",
                bubbleColorOpen: false,
                bubbleBgColorOpen: false,
            };
        },

        watch: {
            htmlModeValue: function (val) {
                this.editor.commands.setContent(val);
                this.updateValue(this.editor.getHTML());
            },
            "currentField.readonly": function (val) {
                if (this.editor) {
                    this.editor.setEditable(!val);
                }
            },
        },

        computed: {
            contentWithTrailingParagraph() {
                let sanitizedValue = this.sanitizeTiptapContent(this.value);

                if (
                    _.isString(sanitizedValue) &&
                    _.endsWith(_.trim(sanitizedValue), "content-block>")
                ) {
                    return sanitizedValue + "<p></p>";
                }

                return sanitizedValue;
            },
            buttons() {
                let tmpButtons = this.currentField.buttons
                    ? this.currentField.buttons
                    : ["bold", "italic"];

                return _.map(tmpButtons, function (button) {
                    return button == "|" || button == "br"
                        ? button
                        : _.camelCase(button);
                });
            },

            headingLevels() {
                return this.currentField.headingLevels
                    ? this.currentField.headingLevels
                    : [1, 2, 3];
            },

            colors() {
                return this.currentField.colors
                    ? this.currentField.colors
                    : this.defaultColors;
            },

            backgroundColors() {
                return this.currentField.backgroundColors
                    ? this.currentField.backgroundColors
                    : this.defaultColors;
            },

            tableCellBackgroundColors() {
                return this.currentField.tableCellBackgroundColors
                    ? this.currentField.tableCellBackgroundColors
                    : this.defaultColors;
            },

            tableCellBorderColors() {
                return this.currentField.tableCellBorderColors
                    ? this.currentField.tableCellBorderColors
                    : this.defaultColors;
            },

            alignments() {
                return this.currentField.alignments
                    ? this.currentField.alignments
                    : ["start", "center", "end", "justify"];
            },

            tableIsActive() {
                if (this.buttons.indexOf("table") > -1) {
                    return this.editor ? this.editor.isActive("table") : false;
                }
                return false;
            },

            alignElements() {
                return this.currentField.alignElements
                    ? this.currentField.alignElements
                    : ["heading", "paragraph"];
            },

            defaultAlignment() {
                return this.currentField.defaultAlignment
                    ? this.currentField.defaultAlignment
                    : "left";
            },

            cssProps() {
                return {
                    "--text-align": this.defaultAlignment,
                };
            },

            htmlTheme() {
                return this.currentField.htmlTheme
                    ? this.currentField.htmlTheme
                    : "";
            },

            showBubbleMenu() {
                return this.currentField.bubbleMenu !== false && this.bubbleMenuButtons.length > 0;
            },

            bubbleMenuButtons() {
                return this.currentField.bubbleMenuButtons
                    ? this.currentField.bubbleMenuButtons
                    : ['bold', 'italic', 'strike'];
            },

            saveAsJson() {
                return this.currentField.saveAsJson
                    ? this.currentField.saveAsJson
                    : false;
            },
        },

        methods: {
            updateValue(value) {
                this.value = this.sanitizeTiptapContent(value);
            },

            switchMode() {
                if (this.mode == "html") {
                    this.editor.commands.setContent(this.htmlModeValue);
                    this.updateValue(this.editor.getHTML());
                    this.mode = "editor";
                } else {
                    this.htmlModeValue = pretty(this.editor.getHTML());
                    this.mode = "html";
                }
            },

            onSyncedField() {
                if (this.editor) {
                    this.editor.setOptions({
                        editable: !this.currentField.readonly,
                    });
                }
                if (!this.initialFieldValue) {
                    this.htmlModeValue = this.value;
                }
            },

            // Bubble menu methods
            toggleBubbleLink() {
                if (this.bubbleLinkOpen) {
                    this.bubbleLinkOpen = false;
                } else {
                    this.bubbleColorOpen = false;
                    this.bubbleBgColorOpen = false;
                    if (this.editor.isActive('link')) {
                        this.bubbleLinkUrl = this.editor.getAttributes('link').href || '';
                    } else {
                        this.bubbleLinkUrl = '';
                    }
                    this.bubbleLinkOpen = true;
                }
            },

            applyBubbleLink() {
                if (this.bubbleLinkUrl) {
                    this.editor.chain().focus().setLink({ href: this.bubbleLinkUrl }).run();
                }
                this.bubbleLinkOpen = false;
            },

            removeBubbleLink() {
                this.editor.chain().focus().unsetLink().run();
                this.bubbleLinkOpen = false;
            },

            toggleBubbleColor() {
                this.bubbleLinkOpen = false;
                this.bubbleBgColorOpen = false;
                this.bubbleColorOpen = !this.bubbleColorOpen;
            },

            toggleBubbleBgColor() {
                this.bubbleLinkOpen = false;
                this.bubbleColorOpen = false;
                this.bubbleBgColorOpen = !this.bubbleBgColorOpen;
            },

            setBubbleColor(color) {
                this.editor.chain().focus().setColor(color).run();
                this.bubbleColorOpen = false;
            },

            unsetBubbleColor() {
                this.editor.chain().focus().unsetColor().run();
                this.bubbleColorOpen = false;
            },

            setBubbleBgColor(color) {
                this.editor.chain().focus().setBackgroundColor(color).run();
                this.bubbleBgColorOpen = false;
            },

            unsetBubbleBgColor() {
                this.editor.chain().focus().unsetBackgroundColor().run();
                this.bubbleBgColorOpen = false;
            },

            closeBubbleMenuDropdowns() {
                this.bubbleLinkOpen = false;
                this.bubbleColorOpen = false;
                this.bubbleBgColorOpen = false;
            },

            async uploadImage(file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('disk', this.imageDisk);
                formData.append('path', this.imagePath);

                try {
                    const response = await axios.post('/nova-tiptap/api/image', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                    return response.data.url;
                } catch (error) {
                    console.error('Image upload failed:', error);
                    return null;
                }
            },
        },

        mounted() {
            this.placeholder = this.currentField.placeholder
                ? this.currentField.placeholder
                : this.currentField.extraAttributes
                ? this.currentField.extraAttributes.placeholder
                : "";

            if (
                this.currentField.imageSettings &&
                this.currentField.imageSettings.path
            ) {
                this.imagePath = this.currentField.imageSettings.path;
            }
            if (
                this.currentField.imageSettings &&
                this.currentField.imageSettings.disk
            ) {
                this.imageDisk = this.currentField.imageSettings.disk;
            }
            if (
                this.currentField.fileSettings &&
                this.currentField.fileSettings.path
            ) {
                this.filePath = this.currentField.fileSettings.path;
            }
            if (
                this.currentField.fileSettings &&
                this.currentField.fileSettings.disk
            ) {
                this.fileDisk = this.currentField.fileSettings.disk;
            }

            let extensions = [
                Document,
                Bold,
                Code,
                Italic,
                Highlight,
                PlaceholderBlockExtension.extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            "data-key": {
                                default: "bogus",
                            },
                        };
                    },
                }),
                VideoContentBlockExtension,
                GalleryContentBlockExtension,
                Link.extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            "tt-mode": {
                                default: "url",
                            },
                            class: String,
                            rel: String,
                            title: String,
                            download: String,
                        };
                    },
                }),
                Strike,
                TextStyle,
                Color,
                BackgroundColorExtension,
                Underline,
                Subscript,
                Superscript,

                Heading.configure({
                    levels: this.headingLevels,
                }).extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            dir: String,
                        };
                    },
                }),
                Blockquote.extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            dir: String,
                        };
                    },
                }),
                BulletList.extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            dir: String,
                        };
                    },
                }),
                HorizontalRule,
                ListItem,
                OrderedList.extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            dir: String,
                        };
                    },
                }),
                HardBreak,
                Paragraph.extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            dir: String,
                            data: String,
                        };
                    },
                }),
                Table.configure({
                    resizable: true,
                }),
                TableRow,
                TableCell,
                TableHeader,
                TextAlign.configure({
                    types: this.alignElements,
                    alignments: this.alignments,
                    defaultAlignment: this.defaultAlignment,
                }),
                UndoRedo,
                Text,
                Gapcursor,
                Placeholder.configure({
                    placeholder: this.placeholder,
                }),
                ImageResize.extend({
                    addAttributes() {
                        return {
                            ...this.parent?.(),
                            "tt-mode": {
                                default: "file",
                            },
                            "tt-link-url": {
                                default: "",
                            },
                            "tt-link-target": {
                                default: "",
                            },
                            "tt-link-mode": {
                                default: "url",
                            },
                            class: {
                                default: null,
                            },
                            title: {
                                default: null,
                            },
                            alt: {
                                default: null,
                            },
                        };
                    },
                }),
                Dropcursor,
                FileHandler.configure({
                    allowedMimeTypes: ['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/svg+xml'],
                    onDrop: (currentEditor, files, pos) => {
                        files.forEach(async (file) => {
                            const url = await this.uploadImage(file);
                            if (url) {
                                currentEditor.chain().focus().setImageAt({ src: url }, pos).run();
                            }
                        });
                    },
                    onPaste: (currentEditor, files) => {
                        files.forEach(async (file) => {
                            const url = await this.uploadImage(file);
                            if (url) {
                                currentEditor.chain().focus().setImage({ src: url }).run();
                            }
                        });
                    },
                }),
                ImageUploadNode.configure({
                    disk: this.imageDisk,
                    path: this.imagePath,
                }),
            ];

            if (
                this.buttons.includes("codeBlock") &&
                this.currentField.syntaxHighlighting
            ) {
                extensions.push(
                    CodeBlockLowlight.extend({
                        addNodeView() {
                            return VueNodeViewRenderer(CodeBlockComponent);
                        },
                    }).configure({
                        lowlight,
                    })
                );
            } else if (this.buttons.includes("codeBlock")) {
                extensions.push(CodeBlock);
            }

            const context = this;

            this.editor = new Editor({
                extensions: extensions,
                content: this.contentWithTrailingParagraph,
                editable: !this.currentField.readonly,
                onCreate() {
                    try {
                        let content = JSON.parse(context.value);
                        let sanitizedContent =
                            context.sanitizeTiptapContent(content);
                        this.commands.setContent(sanitizedContent);
                    } catch {}
                },
                onUpdate() {
                    if (context.saveAsJson) {
                        let jsonContent = this.getJSON();
                        // Sanitize each text node in the JSON content
                        const sanitizeJsonContent = (node) => {
                            if (node.type === "text" && node.text) {
                                node.text = context.sanitizeTiptapContent(
                                    node.text
                                );
                            }
                            if (node.content) {
                                node.content.forEach(sanitizeJsonContent);
                            }
                            return node;
                        };
                        jsonContent.content.forEach(sanitizeJsonContent);
                        context.updateValue(
                            JSON.stringify(jsonContent.content)
                        );
                    } else {
                        context.updateValue(this.getHTML());
                    }
                },
            });

            this.initialFieldValue = this.value;
        },

        beforeDestroy() {
            this.editor.destroy();
        },
    };
</script>

<style lang="scss">
    .nova-tiptap-editor {
        padding-bottom: 20px;
        padding-top: 20px;

        .ProseMirror-focused {
            outline: none;
        }

        img.ProseMirror-selectednode {
            outline: 3px solid var(--primary-30);
        }

        .ProseMirror {
            p.is-editor-empty:first-child::before {
                content: attr(data-placeholder);
                float: left;
                color: #ced4da;
                pointer-events: none;
                height: 0;
            }

            p,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            blockquote,
            ul,
            ol,
            table,
            pre {
                margin-top: 1em;
                line-height: 1.5em;
            }

            h1 {
                font-size: 3em;
            }
            h2 {
                font-size: 2.4em;
            }
            h3 {
                font-size: 1.8em;
            }
            h4 {
                font-size: 1.5em;
            }
            h5 {
                font-size: 1.3em;
            }
            h6 {
                font-size: 1.1em;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                text-align: var(--text-align);
            }

            a {
                color: #0ea5e9;
                text-decoration: underline;
            }

            pre {
                padding-top: 5px;
                padding-bottom: 5px;
                padding-left: 12px;
                padding-right: 12px;
                background-color: #3c4b5f;
                color: white;
                border-radius: 0.125rem;
            }

            p:first-child,
            h1:first-child,
            h2:first-child,
            h3:first-child,
            h4:first-child,
            h5:first-child,
            h6:first-child,
            blockquote:first-child,
            ul:first-child,
            ol:first-child,
            table:first-child,
            pre:first-child {
                margin-top: 0;
            }

            blockquote {
                display: block;
                margin-top: 1.5em;
                margin-bottom: 1.5em;
                padding-left: 15px;
                border-left: 3px solid #dddddd;
            }

            a {
                pointer-events: none;
            }

            ul {
                padding-left: 16px;

                li {
                    list-style: disc;
                }
            }

            ol {
                padding-left: 16px;

                li {
                    list-style: numeric;
                }
            }

            hr {
                border-top: 1px solid #dddddd;
                margin-top: 20px;
                margin-bottom: 10px;
            }

            .tableWrapper {
                margin-top: 15px;
                overflow-x: auto;
            }

            .resize-cursor {
                cursor: ew-resize;
                cursor: col-resize;
            }

            table {
                border-collapse: collapse;
                table-layout: fixed;
                width: 100%;
                overflow: hidden;

                td,
                th {
                    min-width: 1em;
                    border: 2px solid #dddddd;
                    padding: 3px 5px;
                    vertical-align: top;
                    box-sizing: border-box;
                    position: relative;

                    > * {
                        margin-bottom: 0;
                    }
                }

                th {
                    font-weight: bold;
                    text-align: left;
                    background-color: #fafafa;
                }

                .selectedCell:after {
                    z-index: 2;
                    position: absolute;
                    content: "";
                    left: 0;
                    right: 0;
                    top: 0;
                    bottom: 0;
                    background: rgba(200, 200, 255, 0.4);
                    pointer-events: none;
                }

                .column-resize-handle {
                    position: absolute;
                    right: -2px;
                    top: 0;
                    bottom: -2px;
                    width: 4px;
                    background-color: #adf;
                    pointer-events: none;
                }
            }
        }
    }
</style>
