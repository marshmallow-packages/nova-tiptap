import { Node, mergeAttributes } from '@tiptap/core'

export default Node.create({
    name: 'iframe',

    group: 'block',

    atom: true,

    addAttributes() {
        return {
            src: {
                default: null,
            },
            frameborder: {
                default: '0',
            },
            allowfullscreen: {
                default: 'true',
            },
            width: {
                default: '560',
            },
            height: {
                default: '315',
            },
            allow: {
                default: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture',
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'iframe',
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', { class: 'iframe-wrapper' }, ['iframe', mergeAttributes(HTMLAttributes)]]
    },

    addCommands() {
        return {
            setIframe: (options) => ({ commands }) => {
                return commands.insertContent({
                    type: this.name,
                    attrs: options,
                })
            },
        }
    },
})
