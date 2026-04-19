<template>
    <base-button
        :isActive="false"
        :isDisabled="mode != 'editor'"
        :clickMethod="clearFormatting"
        :title="__('clear formatting')"
        icon="clear-formatting"
    >
    </base-button>
</template>

<script>
import BaseButton from './BaseButton.vue';

export default {
    props: ['editor', 'mode'],

    components: {
        BaseButton,
    },

    methods: {
        clearFormatting() {
            this.editor
                .chain()
                .focus()
                .unsetAllMarks()
                .clearNodes()
                .run();
        },
    },
}
</script>
