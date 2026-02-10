<template>
    <span>
        <!-- Advanced Modal -->
        <Modal :show="showAdvancedModal" tabindex="-1">
            <div class="tiptap-modal">
                <div v-if="withFileUpload" class="tiptap-modal__header">
                    <button
                        type="button"
                        class="tiptap-modal__tab"
                        :class="{ 'tiptap-modal__tab--active': linkMode == 'url' }"
                        @click="linkMode = 'url'"
                    >
                        {{ ttt('url') }}
                    </button>
                    <button
                        type="button"
                        class="tiptap-modal__tab"
                        :class="{ 'tiptap-modal__tab--active': linkMode == 'file' }"
                        @click="linkMode = 'file'"
                    >
                        {{ ttt('file upload') }}
                    </button>
                </div>

                <div class="tiptap-modal__body">
                    <!-- URL Mode -->
                    <div v-show="linkMode == 'url'">
                        <div class="tiptap-modal__section">
                            <label class="tiptap-modal__label">{{ ttt('url') }}</label>
                            <input
                                v-model="url"
                                type="text"
                                class="tiptap-modal__input"
                                placeholder="https://"
                            />
                        </div>

                        <label class="tiptap-modal__checkbox">
                            <input type="checkbox" v-model="openInNewWindow" />
                            <span class="tiptap-modal__checkbox-label">{{ ttt('open in new window') }}</span>
                        </label>
                    </div>

                    <!-- File Upload Mode -->
                    <div v-if="withFileUpload" v-show="linkMode == 'file'">
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
                                        style="display: none"
                                    />
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    {{ ttt('select file') }}
                                </label>
                                <span v-if="!file" class="tiptap-modal__file-name">{{ ttt('no file selected') }}</span>
                                <span v-if="file" class="tiptap-modal__file-name">{{ filename }}</span>
                                <button v-if="file" type="button" class="tiptap-modal__file-remove" @click="removeFile()">
                                    <tiptap-icon name="trash" />
                                </button>
                            </div>
                            <div v-if="uploading" class="tiptap-modal__progress">
                                <div class="tiptap-modal__progress-bar" :style="{ width: uploadProgress + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="tiptap-modal__divider"></div>

                    <!-- Advanced Options -->
                    <div class="tiptap-modal__row">
                        <div class="tiptap-modal__section">
                            <label class="tiptap-modal__label">{{ ttt('custom css classes') }}</label>
                            <input v-model="extraClasses" type="text" class="tiptap-modal__input" />
                        </div>
                        <div class="tiptap-modal__section">
                            <label class="tiptap-modal__label">{{ ttt('title') }}</label>
                            <input v-model="title" type="text" class="tiptap-modal__input" />
                        </div>
                    </div>

                    <div class="tiptap-modal__section">
                        <label class="tiptap-modal__label">{{ ttt('Link attributes') }}</label>
                        <div class="tiptap-modal__checkbox-group">
                            <label class="tiptap-modal__checkbox">
                                <input type="checkbox" v-model="nofollow" />
                                <span class="tiptap-modal__checkbox-label">{{ ttt('Prevent search engines from following') }} (nofollow)</span>
                            </label>
                            <label class="tiptap-modal__checkbox">
                                <input type="checkbox" v-model="noopener" />
                                <span class="tiptap-modal__checkbox-label">{{ ttt('Prevent access to opener window') }} (noopener)</span>
                            </label>
                            <label class="tiptap-modal__checkbox">
                                <input type="checkbox" v-model="noreferrer" />
                                <span class="tiptap-modal__checkbox-label">{{ ttt('Hide referrer information') }} (noreferrer)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tiptap-modal__footer">
                    <button type="button" class="tiptap-modal__btn tiptap-modal__btn--ghost" @click="hideAdvancedModal">
                        {{ ttt('cancel') }}
                    </button>
                    <button
                        type="button"
                        class="tiptap-modal__btn tiptap-modal__btn--primary"
                        :disabled="(linkMode == 'url' && !url) || (linkMode == 'file' && !file)"
                        @click="linkMode == 'url' ? setLinkFromUrl($event) : uploadAndAddFile($event)"
                    >
                        {{ linkMode == 'url' ? ttt('set link') : ttt('upload and link file') }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Inline Link Dropdown -->
        <div class="tiptap-link-dropdown" v-click-outside="closeDropdown">
            <base-button
                :isActive="linkIsActive"
                :isDisabled="!linkCanBeUsed"
                :clickMethod="toggleDropdown"
                :title="!linkIsActive ? ttt('set link') : ttt('edit link')"
                icon="link"
            />

            <div v-show="showDropdown" class="tiptap-link-dropdown__menu">
                <div class="tiptap-link-dropdown__content">
                    <input
                        ref="urlInput"
                        v-model="url"
                        type="text"
                        class="tiptap-link-dropdown__input"
                        :placeholder="ttt('Paste a link...')"
                        @keydown.enter.prevent="applyLink"
                    />
                    <button
                        type="button"
                        class="tiptap-link-dropdown__button"
                        :title="ttt('apply')"
                        :disabled="!url"
                        @click="applyLink"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 10 4 15 9 20"></polyline>
                            <path d="M20 4v7a4 4 0 01-4 4H4"></path>
                        </svg>
                    </button>
                    <button
                        v-if="url && linkIsActive"
                        type="button"
                        class="tiptap-link-dropdown__button"
                        :title="ttt('open link')"
                        @click="openLink"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </button>
                    <button
                        v-if="linkIsActive"
                        type="button"
                        class="tiptap-link-dropdown__button tiptap-link-dropdown__button--danger"
                        :title="ttt('remove link')"
                        @click="unsetLink"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                        </svg>
                    </button>
                </div>
                <button
                    v-if="withFileUpload || linkIsActive"
                    type="button"
                    class="tiptap-link-dropdown__advanced"
                    @click="openAdvancedModal"
                >
                    {{ ttt('advanced options') }}
                </button>
            </div>
        </div>
    </span>
</template>

<script>
import { Button } from "laravel-nova-ui";
import buttonHovers from "../../mixins/buttonHovers";
import BaseButton from "./BaseButton.vue";
import { TiptapIcon } from "../icons";
import translations from "../../mixins/translations";

export default {
    mixins: [buttonHovers, translations],

    props: ["button", "editor", "field", "mode", "fileDisk", "filePath"],

    data: function () {
        return {
            showDropdown: false,
            showAdvancedModal: false,
            url: "",
            openInNewWindow: false,
            file: null,
            filename: null,
            uploadProgress: 0,
            uploading: false,
            extraClasses: "",
            linkMode: "url",
            title: "",
            nofollow: false,
            noopener: false,
            noreferrer: false,
        };
    },

    components: {
        TiptapIcon,
        BaseButton,
        Button,
    },

    directives: {
        'click-outside': {
            mounted(el, binding) {
                el._clickOutside = (event) => {
                    if (!(el === event.target || el.contains(event.target))) {
                        binding.value();
                    }
                };
                document.addEventListener('click', el._clickOutside);
            },
            unmounted(el) {
                document.removeEventListener('click', el._clickOutside);
            },
        },
    },

    computed: {
        linkIsActive() {
            return this.editor ? this.editor.isActive("link") : false;
        },

        linkCanBeUsed() {
            return this.editor
                ? this.mode == "editor" && !this.editor.isActive("image")
                : true;
        },

        withFileUpload() {
            return !this.field.linkSettings
                || (typeof this.field.linkSettings.withFileUpload != "boolean" || this.field.linkSettings.withFileUpload);
        },
    },

    methods: {
        toggleDropdown() {
            if (this.showDropdown) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        },

        openDropdown() {
            if (this.linkIsActive) {
                let attributes = this.editor.getAttributes("link");
                this.url = attributes.href || "";
                this.openInNewWindow = attributes.target == "_blank";
                this.linkMode = attributes["tt-mode"] || "url";
                this.extraClasses = attributes.class || "";
                this.title = attributes.title || "";
                this.nofollow = attributes.rel && attributes.rel.indexOf("nofollow") > -1;
                this.noopener = attributes.rel && attributes.rel.indexOf("noopener") > -1;
                this.noreferrer = attributes.rel && attributes.rel.indexOf("noreferrer") > -1;
            } else {
                this.resetForm();
            }

            this.showDropdown = true;
            this.$nextTick(() => {
                if (this.$refs.urlInput) {
                    this.$refs.urlInput.focus();
                }
            });
        },

        closeDropdown() {
            this.showDropdown = false;
        },

        resetForm() {
            this.url = "";
            this.openInNewWindow = false;
            this.nofollow = false;
            this.noopener = false;
            this.noreferrer = false;
            this.linkMode = "url";
            this.extraClasses = "";
            this.title = "";
        },

        applyLink() {
            if (!this.url) return;

            let attributes = {
                href: this.url,
                "tt-mode": "url",
            };

            this.setLink(attributes);
            this.closeDropdown();
        },

        openLink() {
            if (this.url) {
                window.open(this.url, '_blank');
            }
        },

        openAdvancedModal() {
            this.closeDropdown();
            this.showAdvancedModal = true;
        },

        hideAdvancedModal() {
            this.showAdvancedModal = false;
        },

        removeFile() {
            this.file = null;
            this.filename = null;
            this.$refs.fileInput.value = null;
        },

        resetUploading() {
            this.uploading = false;
            this.uploadProgress = 0;
        },

        changeFile(files) {
            if (files.length) {
                this.file = files[0];
                this.filename = this.file.name;
            }
        },

        uploadAndAddFile(e) {
            e.preventDefault();

            this.uploading = true;

            let data = new FormData();
            data.append("file", this.file);
            data.append("disk", this.fileDisk);
            data.append("path", this.filePath);

            const config = {
                headers: { "Content-Type": "multipart/form-data" },
                onUploadProgress: (progressEvent) =>
                    (this.uploadProgress = (progressEvent.loaded / progressEvent.total) * 100),
            };

            axios
                .post("/nova-tiptap/api/file", data, config)
                .then((response) => {
                    this.resetUploading();
                    this.removeFile();

                    let startPosition = response.data.url.lastIndexOf("/");
                    let filename = response.data.url.substr(startPosition + 1);

                    let attributes = {
                        href: response.data.url,
                        "tt-mode": "file",
                        download: filename,
                    };

                    this.setLink(attributes);
                    this.hideAdvancedModal();
                })
                .catch(() => {
                    this.resetUploading();
                    this.removeFile();
                });
        },

        setLinkFromUrl(e) {
            e.preventDefault();

            let attributes = {
                href: this.url,
                "tt-mode": "url",
                target: this.openInNewWindow ? "_blank" : "_self",
            };

            this.setLink(attributes);
            this.hideAdvancedModal();
        },

        setLink(attributes) {
            if (this.extraClasses) {
                attributes.class = this.extraClasses;
            }
            if (this.title) {
                attributes.title = this.title;
            }
            if (this.nofollow || this.noopener || this.noreferrer) {
                attributes.rel = "";
                if (this.nofollow) attributes.rel += "nofollow ";
                if (this.noopener) attributes.rel += "noopener ";
                if (this.noreferrer) attributes.rel += "noreferrer ";
                attributes.rel = attributes.rel.trim();
            }

            if (!this.editor.isActive("image")) {
                this.editor.chain().extendMarkRange("link").unsetLink().run();
                this.editor.chain().focus().setLink(attributes).run();
            }
        },

        unsetLink() {
            this.editor.chain().focus().unsetLink().run();
            this.closeDropdown();
        },
    },
};
</script>
