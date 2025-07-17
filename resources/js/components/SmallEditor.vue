<template>
    <div>
        <div
            class="w-full pt-2 pb-2 mt-3 nova-tiptap-editor form-input-bordered"
        >
            <div class="w-full mb-4 rounded-lg bg-40">
                <template>
                    <div class="p-1">
                        <div
                            v-for="button in buttons"
                            :key="'button-' + button"
                            class="inline-block"
                        >
                            <template v-if="button == 'heading'">
                                <heading-buttons
                                    :headingLevels="[2, 3, 4]"
                                    mode="editor"
                                    :editor="editor"
                                >
                                </heading-buttons>
                            </template>

                            <template v-else-if="button == 'link'">
                                <link-button
                                    :editor="editor"
                                    :button="button"
                                    :field="field"
                                    mode="editor"
                                    :fileDisk="fileDisk"
                                    :filePath="filePath"
                                >
                                </link-button>
                            </template>

                            <template v-else>
                                <normal-button
                                    :editor="editor"
                                    :button="button"
                                    mode="editor"
                                >
                                </normal-button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <editor-content :editor="editor" />
        </div>
    </div>
</template>

<script>
    import { Editor, EditorContent } from "@tiptap/vue-3";

    import Text from "@tiptap/extension-text";
    import Bold from "@tiptap/extension-bold";
    import BulletList from "@tiptap/extension-bullet-list";
    import Highlight from "@tiptap/extension-highlight";
    import HorizontalRule from "@tiptap/extension-horizontal-rule";
    import Italic from "@tiptap/extension-italic";
    import Link from "@tiptap/extension-link";
    import ListItem from "@tiptap/extension-list-item";
    import OrderedList from "@tiptap/extension-ordered-list";
    import Strike from "@tiptap/extension-strike";
    import Subscript from "@tiptap/extension-subscript";
    import Superscript from "@tiptap/extension-superscript";
    import Underline from "@tiptap/extension-underline";

    import Heading from "@tiptap/extension-heading";
    import Document from "@tiptap/extension-document";

    import Paragraph from "@tiptap/extension-paragraph";
    import HardBreak from "@tiptap/extension-hard-break";

    import Dropcursor from "@tiptap/extension-dropcursor";

    import LinkButton from "./buttons/LinkButton";
    import NormalButton from "./buttons/NormalButton";
    import HeadingButtons from "./buttons/HeadingButtons";
    import BaseButton from "./buttons/BaseButton.vue";

    import Gapcursor from "@tiptap/extension-gapcursor";
    import contentSanitizer from "../mixins/contentSanitizer";

    import ImageResize from 'tiptap-extension-resize-image';
    
    export default {
        mixins: [contentSanitizer],
        props: ["value", "imageDisk", "imagePath", "fileDisk", "filePath"],
        components: {
            EditorContent,
            LinkButton,
            NormalButton,
            HeadingButtons,
            BaseButton,
        },
        data() {
            return {
                editor: null,
                localValue: "<p>Fooo</p>",
                buttons: ["heading", "italic", "bold", "link", "bulletList"],
            };
        },
        computed: {
            field: function () {
                return {
                    attribute: _.uniqueId("small_editor_"),
                };
            },
            sanitizedValue: function () {
                return this.sanitizeTiptapContent(this.value);
            },
        },
        methods: {
            updateValue: function (value) {
                this.$emit("input", this.sanitizeTiptapContent(value));
            },
        },
        mounted() {
            let extensions = [
                Document,
                Bold,
                Italic,
                Highlight,
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
                Underline,
                Subscript,
                Superscript,

                Heading.configure({
                    levels: [2, 3, 4],
                }),
                BulletList,
                HorizontalRule,
                ListItem,
                OrderedList,
                HardBreak,
                Paragraph,
                Text,
                Gapcursor,
                Dropcursor,
                ImageResize,
            ];

            const context = this;

            this.editor = new Editor({
                extensions: extensions,
                content: this.sanitizedValue, // Use sanitized value here
                onCreate() {},
                onUpdate() {
                    context.updateValue(this.getHTML());
                    //context.debouncer = _.debounce(callback => callback(), context.updateValue(this.getHTML()))
                },
            });
        },
    };
</script>
