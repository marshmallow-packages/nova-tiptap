<template>
    <node-view-wrapper class="iframe">
        <iframe
            class="iframe__embed"
            :src="node.attrs.src"
            :frameborder="node.attrs.frameborder"
            :allowfullscreen="node.attrs.allowfullscreen"
        ></iframe>
        <input
            v-if="editor.isEditable"
            class="iframe__input"
            type="text"
            :value="node.attrs.src"
            @input="updateSrc($event.target.value)"
            @paste.stop
        />
    </node-view-wrapper>
</template>

<script>
    import { NodeViewWrapper, nodeViewProps } from '@tiptap/vue-3'

    export default {
        components: {
            NodeViewWrapper,
        },

        props: nodeViewProps,

        methods: {
            updateSrc(src) {
                this.updateAttributes({
                    src,
                })
            },
        },
    }
</script>

<style scoped>
    .iframe {
        position: relative;
    }

    .iframe__embed {
        width: 100%;
        min-height: 200px;
        border: 1px solid #ccc;
    }

    .iframe__input {
        width: 100%;
        margin-top: 8px;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
</style>
