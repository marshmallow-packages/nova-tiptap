<template>
    <div class="p-1 text-xs leading-none bg-primary-500">
        <button
            v-for="button in buttons"
            :key="button.title"
            type="button"
            @click="button.command()"
            :disabled="button.disabledIf()"
            v-html="button.title"
            class="px-2 py-px text-xs text-black bg-white rounded-full hover:bg-gray-200"
            style="margin: 4px"
            :style="button.disabledIf() ? 'opacity: 0.5' : ''"
            :class="{
                'cursor-not-allowed': button.disabledIf(),
            }"
        ></button>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                buttons: [
                    {
                        command: () =>
                            this.editor.chain().focus().addRowBefore().run(),
                        disabledIf: () => !this.editor.can().addRowBefore(),
                        title: "Add row before",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().addRowAfter().run(),
                        disabledIf: () => !this.editor.can().addRowAfter(),
                        title: "Add row after",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().deleteRow().run(),
                        disabledIf: () => !this.editor.can().deleteRow(),
                        title: "Delete row",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().addColumnBefore().run(),
                        disabledIf: () => !this.editor.can().addColumnBefore(),
                        title: "Add col before",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().addColumnAfter().run(),
                        disabledIf: () => !this.editor.can().addColumnAfter(),
                        title: "Add col after",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().deleteColumn().run(),
                        disabledIf: () => !this.editor.can().deleteColumn(),
                        title: "Delete col",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().mergeCells().run(),
                        disabledIf: () => !this.editor.can().mergeCells(),
                        title: "Merge cells",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().splitCell().run(),
                        disabledIf: () => !this.editor.can().splitCell(),
                        title: "Split cell",
                    },
                    {
                        command: () =>
                            this.editor
                                .chain()
                                .focus()
                                .toggleHeaderColumn()
                                .run(),
                        disabledIf: () =>
                            !this.editor.can().toggleHeaderColumn(),
                        title: "Header col",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().toggleHeaderRow().run(),
                        disabledIf: () => !this.editor.can().toggleHeaderRow(),
                        title: "Header row",
                    },
                    {
                        command: () =>
                            this.editor
                                .chain()
                                .focus()
                                .toggleHeaderCell()
                                .run(),
                        disabledIf: () => !this.editor.can().toggleHeaderCell(),
                        title: "Header cell",
                    },
                    {
                        command: () =>
                            this.editor.chain().focus().deleteTable().run(),
                        disabledIf: () => !this.editor.can().deleteTable(),
                        title: "Delete table",
                    },
                ],
            };
        },
        props: ["editor"],
    };
</script>
