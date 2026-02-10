<template>
    <node-view-wrapper class="image-upload-wrapper">
        <div
            class="image-upload-dropzone"
            :class="{
                'image-upload-dropzone--dragging': isDragging,
                'image-upload-dropzone--uploading': uploading,
            }"
            @dragenter.prevent="onDragEnter"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
            @click="openFilePicker"
        >
            <input
                ref="fileInput"
                type="file"
                :accept="accept"
                style="display: none"
                @change="onFileSelect"
            />

            <div v-if="!uploading" class="image-upload-dropzone__content">
                <div class="image-upload-dropzone__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
                <div class="image-upload-dropzone__text">
                    <span class="image-upload-dropzone__link">Click to upload</span>
                    <span> or drag and drop</span>
                </div>
                <div class="image-upload-dropzone__hint">
                    PNG, JPG, GIF, WebP or SVG
                </div>
            </div>

            <div v-else class="image-upload-dropzone__progress">
                <div class="image-upload-dropzone__progress-text">
                    Uploading... {{ Math.round(progress) }}%
                </div>
                <div class="image-upload-dropzone__progress-bar">
                    <div
                        class="image-upload-dropzone__progress-fill"
                        :style="{ width: progress + '%' }"
                    ></div>
                </div>
            </div>
        </div>
    </node-view-wrapper>
</template>

<script>
import { NodeViewWrapper } from '@tiptap/vue-3'

export default {
    components: {
        NodeViewWrapper,
    },

    props: {
        node: {
            type: Object,
            required: true,
        },
        updateAttributes: {
            type: Function,
            required: true,
        },
        deleteNode: {
            type: Function,
            required: true,
        },
        editor: {
            type: Object,
            required: true,
        },
        extension: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            isDragging: false,
            uploading: false,
            progress: 0,
        }
    },

    computed: {
        accept() {
            return this.extension.options.accept || 'image/*'
        },
        maxSize() {
            return this.extension.options.maxSize || 10 * 1024 * 1024
        },
    },

    methods: {
        openFilePicker() {
            if (!this.uploading) {
                this.$refs.fileInput.click()
            }
        },

        onDragEnter() {
            this.isDragging = true
        },

        onDragOver() {
            this.isDragging = true
        },

        onDragLeave() {
            this.isDragging = false
        },

        onDrop(event) {
            this.isDragging = false
            const files = event.dataTransfer?.files
            if (files?.length) {
                this.handleFiles(files)
            }
        },

        onFileSelect(event) {
            const files = event.target.files
            if (files?.length) {
                this.handleFiles(files)
            }
        },

        async handleFiles(files) {
            const file = files[0]

            if (!file.type.startsWith('image/')) {
                this.onError('Please select an image file')
                return
            }

            if (this.maxSize > 0 && file.size > this.maxSize) {
                this.onError(`File size exceeds ${Math.round(this.maxSize / 1024 / 1024)}MB limit`)
                return
            }

            await this.uploadFile(file)
        },

        async uploadFile(file) {
            this.uploading = true
            this.progress = 0

            const customUpload = this.extension.options.upload
            if (customUpload) {
                try {
                    const url = await customUpload(file, (progress) => {
                        this.progress = progress
                    })
                    this.insertImage(url)
                } catch (error) {
                    this.onError(error.message || 'Upload failed')
                    this.uploading = false
                }
            } else {
                // Default upload using axios
                const formData = new FormData()
                formData.append('file', file)

                // Get disk and path from editor storage or extension options
                const imageDisk = this.extension.options.disk || ''
                const imagePath = this.extension.options.path || ''
                formData.append('disk', imageDisk)
                formData.append('path', imagePath)

                try {
                    const response = await axios.post('/nova-tiptap/api/image', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                        onUploadProgress: (progressEvent) => {
                            this.progress = (progressEvent.loaded / progressEvent.total) * 100
                        },
                    })
                    this.insertImage(response.data.url)
                } catch (error) {
                    this.onError('Upload failed')
                    this.uploading = false
                }
            }
        },

        insertImage(url) {
            // Replace this node with an actual image
            this.editor
                .chain()
                .focus()
                .deleteNode('imageUpload')
                .setImage({ src: url })
                .run()
        },

        onError(message) {
            const errorHandler = this.extension.options.onError
            if (errorHandler) {
                errorHandler(message)
            } else {
                console.error('Image upload error:', message)
            }
        },
    },
}
</script>

<style>
.image-upload-wrapper {
    margin: 16px 0;
}

.image-upload-dropzone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 160px;
    padding: 32px;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    background: #fafafa;
    cursor: pointer;
    transition: all 0.2s ease;
}

.image-upload-dropzone:hover {
    border-color: #9ca3af;
    background: #f5f5f5;
}

.image-upload-dropzone--dragging {
    border-color: #3b82f6;
    background: #eff6ff;
}

.image-upload-dropzone--uploading {
    cursor: default;
    pointer-events: none;
}

.image-upload-dropzone__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.image-upload-dropzone__icon {
    margin-bottom: 12px;
    color: #9ca3af;
}

.image-upload-dropzone--dragging .image-upload-dropzone__icon {
    color: #3b82f6;
}

.image-upload-dropzone__text {
    font-size: 14px;
    color: #6b7280;
}

.image-upload-dropzone__link {
    color: #3b82f6;
    font-weight: 500;
    text-decoration: underline;
}

.image-upload-dropzone__hint {
    margin-top: 8px;
    font-size: 12px;
    color: #9ca3af;
}

.image-upload-dropzone__progress {
    width: 100%;
    max-width: 300px;
    text-align: center;
}

.image-upload-dropzone__progress-text {
    margin-bottom: 12px;
    font-size: 14px;
    color: #374151;
}

.image-upload-dropzone__progress-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.image-upload-dropzone__progress-fill {
    height: 100%;
    background: #3b82f6;
    transition: width 0.3s ease;
}

/* Dark mode */
.dark .image-upload-dropzone {
    border-color: #4b5563;
    background: #1f2937;
}

.dark .image-upload-dropzone:hover {
    border-color: #6b7280;
    background: #374151;
}

.dark .image-upload-dropzone--dragging {
    border-color: #60a5fa;
    background: #1e3a5f;
}

.dark .image-upload-dropzone__icon {
    color: #6b7280;
}

.dark .image-upload-dropzone--dragging .image-upload-dropzone__icon {
    color: #60a5fa;
}

.dark .image-upload-dropzone__text {
    color: #9ca3af;
}

.dark .image-upload-dropzone__link {
    color: #60a5fa;
}

.dark .image-upload-dropzone__hint {
    color: #6b7280;
}

.dark .image-upload-dropzone__progress-text {
    color: #e5e7eb;
}

.dark .image-upload-dropzone__progress-bar {
    background: #4b5563;
}
</style>
