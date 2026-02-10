<template>
    <div class="tiptap-dropdown" v-click-outside="closeDropdown">
        <button
            type="button"
            class="tiptap-dropdown__trigger"
            :class="{
                'tiptap-dropdown__trigger--active': hasActiveItem,
                'tiptap-dropdown__trigger--disabled': isDisabled,
            }"
            @click="toggleDropdown"
        >
            <span class="tiptap-dropdown__label">
                <slot name="label">{{ label }}</slot>
            </span>
            <svg class="tiptap-dropdown__chevron" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>

        <div v-show="isOpen" class="tiptap-dropdown__menu">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        label: {
            type: String,
            default: '',
        },
        hasActiveItem: {
            type: Boolean,
            default: false,
        },
        isDisabled: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            isOpen: false,
        }
    },

    methods: {
        toggleDropdown() {
            if (this.isDisabled) return;
            this.isOpen = !this.isOpen;
        },
        closeDropdown() {
            this.isOpen = false;
        },
        selectItem() {
            this.closeDropdown();
        },
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
}
</script>
