<template>
    <span style="z-index: 10">
        <Modal :show="imageMenuIsActive" tabindex="-1">
            <div class="tiptap-modal">
                <div v-if="!imageIsActive && withFileUpload" class="tiptap-modal__header">
                    <button
                        type="button"
                        class="tiptap-modal__tab"
                        :class="{ 'tiptap-modal__tab--active': imageMode == 'file' }"
                        @click="imageMode = 'file'"
                    >
                        {{ ttt('file upload') }}
                    </button>
                    <button
                        type="button"
                        class="tiptap-modal__tab"
                        :class="{ 'tiptap-modal__tab--active': imageMode == 'url' }"
                        @click="imageMode = 'url'"
                    >
                        {{ ttt('external url') }}
                    </button>
                </div>

                <div class="tiptap-modal__body">
                    <!-- File Upload / URL for new images -->
                    <div v-if="!imageIsActive">
                        <!-- File Upload Mode -->
                        <div v-if="withFileUpload" v-show="imageMode == 'file'">
                            <div class="tiptap-modal__section">
                                <div
                                    class="tiptap-modal__file-upload"
                                    :class="{ 'opacity-50 pointer-events-none': uploading }"
                                >
                                    <label class="tiptap-modal__file-button">
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            @change="changeFile($event.target.files)"
                                            accept="image/*"
                                            style="display: none"
                                        />
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        {{ ttt('select image') }}
                                    </label>
                                    <span v-if="!preview" class="tiptap-modal__file-name">{{ ttt('no file selected') }}</span>
                                    <img v-if="preview" :src="preview" style="height: 48px; width: auto; border-radius: 6px;" />
                                    <button v-if="file" type="button" class="tiptap-modal__file-remove" @click="removeFile()">
                                        <tiptap-icon name="trash" />
                                    </button>
                                </div>
                                <div v-if="uploading" class="tiptap-modal__progress">
                                    <div class="tiptap-modal__progress-bar" :style="{ width: uploadProgress + '%' }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- URL Mode -->
                        <div v-show="imageMode == 'url'">
                            <div class="tiptap-modal__section">
                                <label class="tiptap-modal__label">{{ ttt('image url') }}</label>
                                <input
                                    v-model="url"
                                    type="text"
                                    class="tiptap-modal__input"
                                    placeholder="https://example.com/image.jpg"
                                />
                            </div>
                        </div>

                        <div class="tiptap-modal__divider"></div>
                    </div>

                    <!-- Image Properties -->
                    <div class="tiptap-modal__section">
                        <label class="tiptap-modal__label">{{ ttt('custom css classes') }}</label>
                        <input v-model="extraClasses" type="text" class="tiptap-modal__input" />
                    </div>

                    <div class="tiptap-modal__row">
                        <div class="tiptap-modal__section">
                            <label class="tiptap-modal__label">{{ ttt('title') }}</label>
                            <input v-model="title" type="text" class="tiptap-modal__input" />
                        </div>
                        <div class="tiptap-modal__section">
                            <label class="tiptap-modal__label">{{ ttt('alt text') }}</label>
                            <input v-model="alt" type="text" class="tiptap-modal__input" />
                        </div>
                    </div>
                </div>

                <div class="tiptap-modal__footer">
                    <button type="button" class="tiptap-modal__btn tiptap-modal__btn--ghost" @click="hideImageMenu">
                        {{ ttt('cancel') }}
                    </button>
                    <button
                        type="button"
                        class="tiptap-modal__btn tiptap-modal__btn--primary"
                        :disabled="!imageIsActive && ((imageMode == 'url' && !url) || (imageMode == 'file' && !file))"
                        @click="imageIsActive ? updateImage($event) : (imageMode == 'url' ? addImageFromUrl($event) : uploadAndAddImage($event))"
                    >
                        {{ imageIsActive ? ttt('update image') : (imageMode == 'url' ? ttt('add image') : ttt('upload image')) }}
                    </button>
                </div>
            </div>
        </Modal>

        <base-button
            :isActive="imageIsActive"
            :isDisabled="mode != 'editor'"
            :clickMethod="showImageMenu"
            icon="image"
            :title="!imageIsActive ? ttt('add image') : ttt('edit image')"
        />
    </span>
</template>

<script>
import BaseButton from './BaseButton.vue';
import { TiptapIcon } from '../icons';
import translations from '../../mixins/translations';

export default {
    mixins: [translations],

    props: [
        'button',
        'editor',
        'field',
        'mode',
        'imageDisk',
        'imagePath',
    ],

    data: function () {
        return {
            imageMenuIsActive: false,
            file: null,
            preview: null,
            url: '',
            uploadProgress: 0,
            uploading: false,
            extraClasses: '',
            imageMode: 'url',
            title: '',
            alt: '',
        }
    },

    components: {
        TiptapIcon,
        BaseButton,
    },

    computed: {
        imageIsActive() {
           return this.editor ? this.editor.isActive('imageResize') : false;
        },
        withFileUpload() {
            return !this.field.imageSettings
                    ||
                    (
                        typeof(this.field.imageSettings.withFileUpload) != 'boolean'
                        || this.field.imageSettings.withFileUpload
                    );
        },
        defaultMode() {
            return this.withFileUpload ? 'file' : 'url';
        }
    },

    methods: {
        showImageMenu() {
            if (this.imageIsActive) {
                let attributes = this.editor.getAttributes('imageResize');
                this.url = attributes.src;
                this.imageMode = attributes['tt-mode'] ? attributes['tt-mode'] : this.defaultMode;
                this.extraClasses = attributes.class ? attributes.class : '';
                this.title = attributes.title ? attributes.title : '';
                this.alt = attributes.alt ? attributes.alt : '';
            } else {
                this.url = '';
                this.imageMode = this.defaultMode;
                this.extraClasses = '';
                this.title = '';
                this.alt = '';
            }
            
            this.imageMenuIsActive = true;
        },

        hideImageMenu() {
            this.imageMenuIsActive = false;
        },

        removeFile() {
            this.file = null;
            this.preview = null;
            this.$refs.fileInput.value=null;
         },

         resetUploading() {
            this.uploading = false;
            this.uploadProgress = 0;
         },

         changeFile(files) {
            if (files.length) {
                this.file = files[0];
                this.preview = URL.createObjectURL(this.file);
            }
         },

        addImageFromUrl(e) {
            e.preventDefault();

            let attributes = {
                src: this.url,
                'tt-mode': 'url',
                class: this.extraClasses,
                title: this.title,
                alt: this.alt,
            };

            this.editor.chain().focus().setImage(attributes).run();
            
            this.hideImageMenu();
        },

        uploadAndAddImage(e) {
            e.preventDefault();

            this.uploading = true;
            
            let data = new FormData();
            data.append('file', this.file);   
            data.append('disk', this.imageDisk);   
            data.append('path', this.imagePath);   

            const config = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: progressEvent => this.uploadProgress = (progressEvent.loaded / progressEvent.total) * 100
            };

            axios.post('/nova-tiptap/api/image', data, config)
                .then(function (response) {
                    this.resetUploading();
                    this.removeFile();
                    
                    let attributes = {
                        src: response.data.url,
                        'tt-mode': 'file',
                        class: this.extraClasses,
                        title: this.title,
                        alt: this.alt,
                    };

                    this.editor.chain().focus().setImage(attributes).run();
                    
                    this.hideImageMenu();
                }.bind(this))
                .catch(function (error) {
                    this.resetUploading();
                    this.removeFile();
                    console.log(error);
                }.bind(this));

            
        },

        updateImage(e) {
            e.preventDefault();

            let attributes = {
                class: this.extraClasses,
                title: this.title,
                alt: this.alt,
            };

            this.editor.chain().focus().updateAttributes('imageResize', attributes).run();
            
            this.hideImageMenu();
        }
    },
}
</script>
