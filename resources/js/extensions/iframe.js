import { Node } from '@tiptap/core'

export const Iframe = Node.create({
    name: 'iframe',
    group: 'block',
    atom: true,

    addAttributes() {
        return {
            src: {
                default: null,
            },
            width: {
                default: '560',
            },
            height: {
                default: '315',
            },
            frameborder: {
                default: '0',
            },
            allow: {
                default: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
            },
            allowfullscreen: {
                default: 'true',
            },
            title: {
                default: '',
            },
            style: {
                default: null,
            },
            class: {
                default: null,
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
        return ['iframe', HTMLAttributes]
    },
})

export default Iframe
