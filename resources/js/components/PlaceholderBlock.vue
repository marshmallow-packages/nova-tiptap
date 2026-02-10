<template>
  <node-view-wrapper>
    <div
        class="
            w-full flex justify-between items-center
            bg-30 px-3 py-2 rounded-lg shadow-sm my-4
        "
        contenteditable="false"
    >
        <div
            class="cursor-move pr-4 text-60 text-lg"
            contenteditable="false"
            draggable="true"
            data-drag-handle
        >
            <tiptap-icon name="grip" />
        </div>

        <div class="w-full">
            <node-view-content class="text-80" contenteditable="false" />
        </div>

        <div
            class="pl-4 text-60 text-lg cursor-pointer"
            contenteditable="false"
            @click="deleteBlock"
        >
            <tiptap-icon name="trash" />
        </div>
    </div>
  </node-view-wrapper>
</template>

<script>
import { NodeViewWrapper, NodeViewContent, nodeViewProps } from '@tiptap/vue-3'
import { TiptapIcon } from './icons'

export default {
  props: nodeViewProps,

  components: {
    NodeViewWrapper,
    NodeViewContent,
    TiptapIcon
  },

  methods: {
      deleteBlock() {
          this.$el.remove();
      }
  }
}
</script>
