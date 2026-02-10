<template>
    <span class="tiptap-icon">
        <component :is="iconComponent" />
    </span>
</template>

<style scoped>
.tiptap-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    line-height: 0;
}
.tiptap-icon :deep(svg) {
    display: block;
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}
</style>

<script>
    import BoldIcon from './BoldIcon.vue'
    import ItalicIcon from './ItalicIcon.vue'
    import UnderlineIcon from './UnderlineIcon.vue'
    import StrikethroughIcon from './StrikethroughIcon.vue'
    import SubscriptIcon from './SubscriptIcon.vue'
    import SuperscriptIcon from './SuperscriptIcon.vue'
    import ParagraphIcon from './ParagraphIcon.vue'
    import OrderedListIcon from './OrderedListIcon.vue'
    import BulletListIcon from './BulletListIcon.vue'
    import HorizontalRuleIcon from './HorizontalRuleIcon.vue'
    import BlockquoteIcon from './BlockquoteIcon.vue'
    import HighlighterIcon from './HighlighterIcon.vue'
    import RtlIcon from './RtlIcon.vue'
    import AlignLeftIcon from './AlignLeftIcon.vue'
    import AlignCenterIcon from './AlignCenterIcon.vue'
    import AlignRightIcon from './AlignRightIcon.vue'
    import AlignJustifyIcon from './AlignJustifyIcon.vue'
    import CodeIcon from './CodeIcon.vue'
    import CodeBlockIcon from './CodeBlockIcon.vue'
    import TableIcon from './TableIcon.vue'
    import UndoIcon from './UndoIcon.vue'
    import RedoIcon from './RedoIcon.vue'
    import LinkIcon from './LinkIcon.vue'
    import UnlinkIcon from './UnlinkIcon.vue'
    import ImageIcon from './ImageIcon.vue'
    import ImageUploadIcon from './ImageUploadIcon.vue'
    import ColorIcon from './ColorIcon.vue'
    import BackgroundColorIcon from './BackgroundColorIcon.vue'
    import ClearColorIcon from './ClearColorIcon.vue'
    import PlaceholderBlockIcon from './PlaceholderBlockIcon.vue'
    import ContentBlockIcon from './ContentBlockIcon.vue'
    import TrashIcon from './TrashIcon.vue'
    import GripIcon from './GripIcon.vue'
    import ChevronUpIcon from './ChevronUpIcon.vue'
    import ChevronDownIcon from './ChevronDownIcon.vue'
    import CloseCircleIcon from './CloseCircleIcon.vue'

    const iconMap = {
        'bold': BoldIcon,
        'italic': ItalicIcon,
        'underline': UnderlineIcon,
        'strikethrough': StrikethroughIcon,
        'strike': StrikethroughIcon,
        'subscript': SubscriptIcon,
        'superscript': SuperscriptIcon,
        'paragraph': ParagraphIcon,
        'ordered-list': OrderedListIcon,
        'orderedList': OrderedListIcon,
        'list-ol': OrderedListIcon,
        'bullet-list': BulletListIcon,
        'bulletList': BulletListIcon,
        'list-ul': BulletListIcon,
        'horizontal-rule': HorizontalRuleIcon,
        'horizontalRule': HorizontalRuleIcon,
        'blockquote': BlockquoteIcon,
        'quote-right': BlockquoteIcon,
        'highlighter': HighlighterIcon,
        'highlight': HighlighterIcon,
        'rtl': RtlIcon,
        'paragraph-rtl': RtlIcon,
        'align-left': AlignLeftIcon,
        'alignLeft': AlignLeftIcon,
        'align-center': AlignCenterIcon,
        'alignCenter': AlignCenterIcon,
        'align-right': AlignRightIcon,
        'alignRight': AlignRightIcon,
        'align-justify': AlignJustifyIcon,
        'alignJustify': AlignJustifyIcon,
        'code': CodeIcon,
        'code-block': CodeBlockIcon,
        'codeBlock': CodeBlockIcon,
        'file-code': CodeBlockIcon,
        'table': TableIcon,
        'undo': UndoIcon,
        'undo-alt': UndoIcon,
        'redo': RedoIcon,
        'redo-alt': RedoIcon,
        'link': LinkIcon,
        'unlink': UnlinkIcon,
        'image': ImageIcon,
        'image-upload': ImageUploadIcon,
        'imageUpload': ImageUploadIcon,
        'color': ColorIcon,
        'text-color': ColorIcon,
        'a': ColorIcon,
        'background-color': BackgroundColorIcon,
        'backgroundColor': BackgroundColorIcon,
        'swatchbook': BackgroundColorIcon,
        'pen-fancy': BackgroundColorIcon,
        'clear-color': ClearColorIcon,
        'droplet-slash': ClearColorIcon,
        'placeholder-block': PlaceholderBlockIcon,
        'placeholderBlock': PlaceholderBlockIcon,
        'cube': PlaceholderBlockIcon,
        'content-block': ContentBlockIcon,
        'contentBlock': ContentBlockIcon,
        'cubes': ContentBlockIcon,
        'trash': TrashIcon,
        'trash-alt': TrashIcon,
        'grip': GripIcon,
        'grip-horizontal': GripIcon,
        'chevron-up': ChevronUpIcon,
        'angle-up': ChevronUpIcon,
        'chevron-down': ChevronDownIcon,
        'angle-down': ChevronDownIcon,
        'close-circle': CloseCircleIcon,
        'times-circle': CloseCircleIcon,
    }

    export default {
        props: {
            name: {
                type: String,
                required: true,
            },
        },

        computed: {
            iconComponent() {
                return iconMap[this.name] || null
            },
        },
    }
</script>
