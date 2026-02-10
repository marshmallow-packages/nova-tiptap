<template>
    <dropdown-button
        :label="activeHeadingLabel"
        :hasActiveItem="hasActiveHeading"
        :isDisabled="mode != 'editor'"
    >
        <template #label>
            <span class="tiptap-dropdown__heading-label">{{ activeHeadingLabel }}</span>
        </template>

        <button
            v-for="level in headingLevels"
            :key="'heading-'+level"
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': headingIsActive(level) }"
            @click="setHeading(level)"
        >
            <span class="tiptap-dropdown__item-icon">H{{ level }}</span>
            <span class="tiptap-dropdown__item-label">{{ __('heading :level', {'level': level}) }}</span>
        </button>

        <button
            type="button"
            class="tiptap-dropdown__item"
            :class="{ 'tiptap-dropdown__item--active': paragraphIsActive }"
            @click="setParagraph"
        >
            <tiptap-icon name="paragraph" />
            <span class="tiptap-dropdown__item-label">{{ __('paragraph') }}</span>
        </button>
    </dropdown-button>
</template>

<script>
import DropdownButton from './DropdownButton.vue';
import { TiptapIcon } from '../icons';
import translations from '../../mixins/translations';

export default {
    mixins: [translations],

    props: ['headingLevels', 'editor', 'mode'],

    components: {
        DropdownButton,
        TiptapIcon,
    },

    computed: {
        hasActiveHeading() {
            if (!this.editor) return false;
            for (let level of this.headingLevels) {
                if (this.editor.isActive('heading', { level })) {
                    return true;
                }
            }
            return false;
        },
        activeHeadingLabel() {
            if (!this.editor) return 'H1';
            for (let level of this.headingLevels) {
                if (this.editor.isActive('heading', { level })) {
                    return 'H' + level;
                }
            }
            return 'H1';
        },
        paragraphIsActive() {
            return this.editor ? this.editor.isActive('paragraph') : false;
        },
    },

    methods: {
        headingIsActive(level) {
            return this.editor ? this.editor.isActive('heading', { level }) : false;
        },
        setHeading(level) {
            if (this.editor) {
                this.editor.chain().focus().toggleHeading({ level }).run();
            }
        },
        setParagraph() {
            if (this.editor) {
                this.editor.chain().focus().setParagraph().run();
            }
        },
    },
}
</script>
