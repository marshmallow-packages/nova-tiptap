<template>
    <Dropdown
        :should-close-on-blur="false"
        placement="top-center">
        <base-button
            title="Color"
            style="top: -2px;"
            :clickMethod="buttonClickMethod">
            <font-awesome-icon :icon="['fas', 'a']" />
            <div class="color-indicator" :style="{ 'background-color': currentColor }"></div>
        </base-button>

        <template #menu>
            <DropdownMenu width="250">
                <div class="square-grid">
                    <div
                        v-for="(color, index) in colors"
                        :key="`color-${index}`"
                        :style="{ 'background-color': color }"
                        class="color-option"
                        @click="setColor(color, true)">
                    </div>
                    <div class="custom-color-option">
                        <font-awesome-icon
                            :icon="['fas', 'droplet-slash']"
                            class="custom-color-option-icon"
                            @click="setColor(undefined, true)" />
                    </div>
                    <div class="custom-color-option">
                        <font-awesome-icon
                            :icon="['fas', 'swatchbook']"
                            class="custom-color-option-icon"
                            :style="{ color: currentColor }"
                            @click="customColorClick" />
                        <input
                            type="color"
                            ref="colorInput"
                            @input="setColor($event.target.value)"
                            class="custom-color-option-input" />
                    </div>
                </div>
            </DropdownMenu>
        </template>
    </Dropdown>
</template>

<script>
import BaseButton from "./BaseButton.vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default {
    props: ['editor', 'colors'],
    components: {BaseButton, FontAwesomeIcon},
    computed: {
        currentColor() {
            if (!this.editor) {
                return '#000';
            }

            const attributes = this.editor.getAttributes('textStyle');

            if (!Object.prototype.hasOwnProperty.call(attributes, 'color')) {
                return '#000';
            }

            const {color} = attributes;

            if (color && color.startsWith('rgb')) {
                return this.rgbToHex(color);
            }

            return color;
        },
    },
    methods: {
        buttonClickMethod() {},
        rgbToHex(rgb) {
            const rgbArray = rgb.match(/\d+/g);

            const hexColor = rgbArray
                .map((value) => parseInt(value).toString(16).padStart(2, '0'))
                .join('');

            return `#${hexColor}`;
        },
        customColorClick() {
            this.$refs.colorInput.focus();
            this.$refs.colorInput.value = this.currentColor;
            this.$refs.colorInput.click();
        },
        setColor(color, shouldClose = false) {
            if (!color) {
                this.editor.commands.unsetColor();
            } else {
                this.editor.commands.setColor(color);
            }

            if (shouldClose) {
                const dropdownOverlay = document.querySelector('[dusk="dropdown-overlay"]');

                if (dropdownOverlay) {
                    dropdownOverlay.click();
                }
            }
        }
    },
};
</script>

<style lang="scss">
.color-indicator {
    width: 100%;
    margin-top: 2px;
    height: 2px;
    display: block;
}

.square-grid {
    display: grid;
    grid-auto-rows: minmax(0, 1fr);
    grid-template-columns: repeat(4, minmax(0, 1fr));

    &:before {
        content: '';
        width: 0;
        padding-bottom: 100%;
        grid-row: 1 / 1;
        grid-column: 1 / 1;
    }

    & > *:first-child {
        grid-row: 1 / 1;
        grid-column: 1 / 1;
    }
}

.color-option {
    display: block;
    transition: all 150ms ease-in;
    cursor: pointer;

    &:hover {
        transform: scale(0.85);
    }
}

.custom-color-option {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;

    .custom-color-option-icon {
        transition: all 150ms ease-in;
        width: 24px;
        height: 24px;
        cursor: pointer;
    }

    .custom-color-option-input {
        opacity: 0;
        position: absolute;
        width: 1px;
        height: 1px;
    }

    &:hover {
        .custom-color-option-icon {
            opacity: .6;
        }
    }
}
</style>
