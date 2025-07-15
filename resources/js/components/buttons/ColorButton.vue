<template>
    <Dropdown
        :should-close-on-blur="false"
        placement="top-center">
        <base-button
            :title="mode === 'color' ? 'Color' : 'Background color'"
            style="top: -2px;"
            :clickMethod="buttonClickMethod">
            <font-awesome-icon :icon="['fas', mode === 'color' ? 'a' : 'pen-fancy']" />
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
import colorUtils from '../../mixins/colorUtils';

export default {
    props: ['editor', 'colors', 'mode'],
    components: {BaseButton, FontAwesomeIcon},
    mixins: [colorUtils],
    computed: {
        currentColor() {
            if (!this.editor) {
                return '#000000';
            }

            const attributes = this.editor.getAttributes('textStyle');

            if (
                (this.mode === 'color' && !Object.prototype.hasOwnProperty.call(attributes, 'color')) ||
                (this.mode === 'backgroundColor' && !Object.prototype.hasOwnProperty.call(attributes, 'backgroundColor'))
            ) {
                return '#000000';
            }

            const color = (this.mode === 'color') ? attributes.color : attributes.backgroundColor;

            if (color && color.startsWith('rgb')) {
                return this.rgbToHex(color);
            }

            return color;
        },
    },
    methods: {
        buttonClickMethod() {},
        customColorClick() {
            this.$refs.colorInput.focus();
            this.$refs.colorInput.value = this.currentColor;
            this.$refs.colorInput.click();
        },
        setColor(color, shouldClose = false) {
            if (!color) {
                if (this.mode === 'color') {
                    this.editor.commands.unsetColor();
                } else {
                    this.editor.commands.unsetBackgroundColor();
                }
            } else {
                if (this.mode === 'color') {
                    this.editor.commands.setColor(color);
                } else {
                    this.editor.commands.setBackgroundColor(color);
                }
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
