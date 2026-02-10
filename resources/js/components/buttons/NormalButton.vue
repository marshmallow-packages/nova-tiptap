<template>
    <base-button
        :isActive="buttonIsActive"
        :isDisabled="mode != 'editor'"
        :clickMethod="toggleButton"
        :title="__(button)"
        :icon="icon"
        :innerHtml="innerHtml"
    >
    </base-button>
</template>

<script>

import BaseButton from './BaseButton.vue';

export default {
    props: ['button', 'editor', 'mode'],

    components: {
        BaseButton,
    },

    computed: {
         buttonIsActive() {
            if (!this.editor) {
                return false;
            }

            return this.editor.isActive(this.button);
        },

        icon() {
            const iconMap = {
                'bold': 'bold',
                'italic': 'italic',
                'highlight': 'highlighter',
                'strike': 'strikethrough',
                'underline': 'underline',
                'blockquote': 'blockquote',
                'bulletList': 'bullet-list',
                'orderedList': 'ordered-list',
                'horizontalRule': 'horizontal-rule',
                'table': 'table',
                'subscript': 'subscript',
                'superscript': 'superscript',
                'paragraph': 'paragraph',
                'code': 'code',
                'codeBlock': 'code-block',
                'imageUpload': 'image-upload',
            }

            return iconMap[this.button] || null
        },

        innerHtml() {
            if (this.button == 'hardBreak') {
                return '<span> BR </span>';
            }

            return null;
        }
    },

    methods: {
        toggleButton() {
            var command = this.editor.chain().focus();
            if (this.button == 'bold') {
                command.toggleBold();
            } else if (this.button == 'code') {
                command.toggleCode();
            } else if (this.button == 'italic') {
                command.toggleItalic();
            } else if (this.button == 'highlight') {
                command.toggleHighlight();
            } else if (this.button == 'strike') {
                command.toggleStrike();
            } else if (this.button == 'underline') {
                command.toggleUnderline();
            } else if (this.button == 'subscript') {
                command.toggleSubscript();
            } else if (this.button == 'superscript') {
                command.toggleSuperscript();
            } else if (this.button == 'blockquote') {
                command.toggleBlockquote();
            } else if (this.button == 'bulletList') {
                command.toggleBulletList();
            } else if (this.button == 'orderedList') {
                command.toggleOrderedList();
            } else if (this.button == 'codeBlock') {
                command.toggleCodeBlock();
            } else if (this.button == 'horizontalRule') {
                command.setHorizontalRule();
            } else if (this.button == 'table') {
                command.insertTable({ rows: 3, cols: 3, withHeaderRow: true });
            } else if (this.button == 'hardBreak') {
              command.setHardBreak();
            } else if (this.button == 'paragraph') {
              command.setParagraph();
            } else if (this.button == 'imageUpload') {
              command.setImageUpload();
            }

            command.run();
        }
    },
}
</script>
