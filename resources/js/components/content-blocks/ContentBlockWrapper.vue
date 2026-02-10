<template>
  <node-view-wrapper>
    <div
        class="
            relative w-full my-8
        "
        contenteditable="false"
    >
        <div
            class="flex justify-center"
        >
            <div
                v-text="label"
                class="
                    bg-60 rounded-t text-white uppercase font-bold text-xs
                    px-2 pt-1
                "
                draggable="true"
                data-drag-handle
            >

            </div>
        </div>

        <div
            class="
                w-full flex justify-between items-center
                bg-60 px-3 py-2 rounded-lg
            "
        >
            <div
                class="cursor-move pr-4 text-white text-lg"
                draggable="true"
                data-drag-handle
            >
                <tiptap-icon name="grip" />
            </div>

            <div class="w-full">
                <slot />
            </div>

            <div
                class="pl-4 text-white text-lg cursor-pointer"
                @click="deleteBlock"
            >
                <tiptap-icon name="trash" />
            </div>

        </div>

        <div
            class="flex justify-center hidden"
        >
            <div
                v-text="nodeKey"
                class="
                   text-xs
                   pt-1
                "

            >

            </div>
        </div>
    </div>
  </node-view-wrapper>
</template>

<script>
import { NodeViewWrapper } from '@tiptap/vue-3'
import { TiptapIcon } from '../icons'

export default {
    props: {
        label: String,
        nodeKey: String|Number,
    },

    components: {
        NodeViewWrapper,
        TiptapIcon
    },

    methods: {
        deleteBlock() {
            this.$el.remove();
        },
    }
}
</script>
