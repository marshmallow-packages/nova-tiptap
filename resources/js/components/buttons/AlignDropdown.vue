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
            v-if="alignments.includes('left')"
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': isActive('left') }"
            @click="setAlignment('left')"
        >
            <tiptap-icon name="alignLeft" />
            <span class="tiptap-dropdown__item-label">{{ __('align left') }}</span>
        </button>

        <button
            v-if="alignments.includes('center')"
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': isActive('center') }"
            @click="setAlignment('center')"
        >
            <tiptap-icon name="alignCenter" />
            <span class="tiptap-dropdown__item-label">{{ __('align center') }}</span>
        </button>

        <button
            v-if="alignments.includes('right')"
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': isActive('right') }"
            @click="setAlignment('right')"
        >
            <tiptap-icon name="alignRight" />
            <span class="tiptap-dropdown__item-label">{{ __('align right') }}</span>
        </button>

        <button
            v-if="alignments.includes('justify')"
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': isActive('justify') }"
            @click="setAlignment('justify')"
        >
            <tiptap-icon name="alignJustify" />
            <span class="tiptap-dropdown__item-label">{{ __('justify') }}</span>
        </button>
    </dropdown-button>
</template>

<script>
import DropdownButton from './DropdownButton.vue';
import { TiptapIcon } from '../icons';
import translations from '../../mixins/translations';

export default {
    mixins: [translations],

    props: ['editor', 'mode', 'alignments', 'alignElements', 'defaultAlignment'],

    components: {
        DropdownButton,
        TiptapIcon,
    },

    computed: {
        hasActiveItem() {
            if (!this.editor) return false;
            for (let align of this.alignments) {
                if (align !== this.defaultAlignment && this.isActive(align)) {
                    return true;
                }
            }
            return false;
        },
        activeIcon() {
            if (!this.editor) return 'alignLeft';
            if (this.isActive('center')) return 'alignCenter';
            if (this.isActive('right')) return 'alignRight';
            if (this.isActive('justify')) return 'alignJustify';
            return 'alignLeft';
        },
        activeLabel() {
            if (!this.editor) return this.__('align left');
            if (this.isActive('center')) return this.__('align center');
            if (this.isActive('right')) return this.__('align right');
            if (this.isActive('justify')) return this.__('justify');
            return this.__('align left');
        },
    },

    methods: {
        isActive(alignment) {
            if (!this.editor) return false;
            return this.editor.isActive({ textAlign: alignment });
        },
        setAlignment(alignment) {
            if (this.editor) {
                this.editor.chain().focus().setTextAlign(alignment).run();
            }
        },
    },
}
</script>
