<template>
    <button
        type="button"
        class="tiptap-button"
        :class="{
            'tiptap-button--active': isActive,
            'tiptap-button--disabled': isDisabled,
        }"
        @mouseover="hovered = true"
        @mouseout="hovered = false"
        @click="callClickMethod"
    >
        <div
            class="tiptap-button__tooltip"
            v-show="hovered"
        >
            <div v-html="title"></div>
        </div>

        <template v-if="iconName">
            <tiptap-icon :name="iconName" />
        </template>

        <template v-else-if="innerHtml">
            <span class="tiptap-button__text" v-html="innerHtml"></span>
        </template>

        <template v-else>
            <slot />
        </template>
    </button>
</template>

<script>
import { TiptapIcon } from '../icons'

export default {
    data() {
        return {
            hovered: false,
        }
    },
    props: [
        'clickMethod',
        'clickMethodParameters',
        'title',
        'isActive',
        'isDisabled',
        'icon',
        'innerHtml',
    ],

    components: {
        TiptapIcon,
    },

    computed: {
        iconName() {
            if (!this.icon) {
                return null
            }

            // Handle legacy Font Awesome array format ['fas', 'icon-name']
            if (Array.isArray(this.icon)) {
                return this.icon[1]
            }

            // Handle new string format
            return this.icon
        },
    },

    methods: {
        callClickMethod() {
            let tmpParams = this.clickMethodParameters
            if (tmpParams) {

                if (!typeof(tmpParams) != 'object') {
                    tmpParams = [tmpParams];
                }

                this.clickMethod(...tmpParams);
            } else {
                this.clickMethod();
            }
        }
    }
}
</script>
