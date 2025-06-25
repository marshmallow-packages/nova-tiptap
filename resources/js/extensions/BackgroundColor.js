import '@tiptap/extension-text-style';
import { Extension } from '@tiptap/core';

export default Extension.create({
    name: 'background-color',
    addOptions() {
        return {
            types: ['textStyle'],
        }
    },
    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    backgroundColor: {
                        default: null,
                        parseHTML: element => element.style.backgroundColor?.replace(/['"]+/g, ''),
                        renderHTML: attributes => {
                            if (!attributes.backgroundColor) {
                                return {}
                            }

                            return {
                                style: `background-color: ${attributes.backgroundColor}`,
                            }
                        },
                    },
                },
            },
        ]
    },
    addCommands() {
        return {
            setBackgroundColor: color => ({ chain }) => {
                return chain()
                    .setMark('textStyle', { backgroundColor: color })
                    .run()
            },
            unsetBackgroundColor: () => ({ chain }) => {
                return chain()
                    .setMark('textStyle', { backgroundColor: null })
                    .removeEmptyTextStyle()
                    .run()
            },
        }
    },
})
