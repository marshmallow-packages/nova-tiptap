<template>
    <dropdown-button
        :label="activeLabel"
        :hasActiveItem="hasActiveItem"
        :isDisabled="mode != 'editor'"
    >
        <template #label>
            <tiptap-icon :name="activeIcon" />
        </template>

        <button
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': bulletListIsActive }"
            @click="toggleBulletList"
        >
            <tiptap-icon name="bulletList" />
            <span class="tiptap-dropdown__item-label">{{ __('bullet list') }}</span>
        </button>

        <button
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': orderedListIsActive }"
            @click="toggleOrderedList"
        >
            <tiptap-icon name="orderedList" />
            <span class="tiptap-dropdown__item-label">{{ __('ordered list') }}</span>
        </button>
    </dropdown-button>
</template>

<script>
import DropdownButton from './DropdownButton.vue';
import { TiptapIcon } from '../icons';
import translations from '../../mixins/translations';

export default {
    mixins: [translations],

    props: ['editor', 'mode'],

    components: {
        DropdownButton,
        TiptapIcon,
    },

    computed: {
        bulletListIsActive() {
            return this.editor ? this.editor.isActive('bulletList') : false;
        },
        orderedListIsActive() {
            return this.editor ? this.editor.isActive('orderedList') : false;
        },
        hasActiveItem() {
            return this.bulletListIsActive || this.orderedListIsActive;
        },
        activeIcon() {
            if (this.orderedListIsActive) return 'orderedList';
            return 'bulletList';
        },
        activeLabel() {
            if (this.orderedListIsActive) return this.__('ordered list');
            return this.__('bullet list');
        },
    },

    methods: {
        toggleBulletList() {
            if (this.editor) {
                this.editor.chain().focus().toggleBulletList().run();
            }
        },
        toggleOrderedList() {
            if (this.editor) {
                this.editor.chain().focus().toggleOrderedList().run();
            }
        },
    },
}
</script>
