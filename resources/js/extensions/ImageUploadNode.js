import { Node, mergeAttributes } from '@tiptap/core'
import { VueNodeViewRenderer } from '@tiptap/vue-3'
import ImageUploadNodeView from '../components/nodes/ImageUploadNodeView.vue'

export default Node.create({
    name: 'imageUpload',

    group: 'block',

    draggable: true,

    addOptions() {
        return {
            accept: 'image/*',
            maxSize: 10 * 1024 * 1024, // 10MB default
            upload: null,
            onError: null,
        }
    },

    addAttributes() {
        return {
            uploading: {
                default: false,
            },
            progress: {
                default: 0,
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'div[data-type="image-upload"]',
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', mergeAttributes({ 'data-type': 'image-upload' }, HTMLAttributes)]
    },

    addNodeView() {
        return VueNodeViewRenderer(ImageUploadNodeView)
    },

    addCommands() {
        return {
            setImageUpload: () => ({ commands }) => {
                return commands.insertContent({
                    type: this.name,
                })
            },
        }
    },
})
