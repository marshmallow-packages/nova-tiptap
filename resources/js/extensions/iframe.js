import { Node, mergeAttributes } from '@tiptap/core'
import { VueNodeViewRenderer } from '@tiptap/vue-3'
import IframeComponent from '../components/IframeComponent.vue'

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
                default: 0,
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

    addNodeView() {
        return VueNodeViewRenderer(IframeComponent)
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
