<template>
    <Dropdown
        :should-close-on-blur="false"
        placement="top-center">
        <base-button
            :clickMethod="buttonClickMethod"
            :title="'Table'"
            icon="table"
        >
        </base-button>

        <template #menu>
            <DropdownMenu v-if="!isActive" width="160">
                <div class="table-create-square-grid bg-gray-200 dark:bg-gray-700">
                    <template v-for="row in 10">
                        <template v-for="column in 10">
                            <div
                                class="cursor-pointer"
                                :class="{
                                    'bg-white dark:bg-gray-900': !(row <= hover.row && column <= hover.col),
                                    'bg-primary-500': row <= hover.row && column <= hover.col,
                                }"
                                @mouseover="setHover(row, column)"
                                @click="insertTable"
                            ></div>
                        </template>
                    </template>
                </div>
                <div class="py-1 text-center text-sm">
                    Selected {{ hover.row }}x{{ hover.col }}
                </div>
            </DropdownMenu>
            <DropdownMenu v-else width="230">
                <Dropdown
                    placement="right-center">
                    <DropdownMenuItem as="button" class="no-ring no no-padding">
                        <div class="flex justify-between items-center px-3 py-1.5" @click="toggleSubmenus('table-row')">
                            Row
                            <IconArrow class="-rotate-90" />
                        </div>
                    </DropdownMenuItem>

                    <template #menu>
                        <DropdownMenu width="160">
                            <div class="table-row"></div>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().addRowBefore()"
                                @click="runTableCommand('addRowBefore')">
                                Add row before
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().addRowAfter()"
                                @click="runTableCommand('addRowAfter')">
                                Add row after
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().deleteRow()"
                                @click="runTableCommand('deleteRow')">
                                Delete row
                            </DropdownMenuItem>
                        </DropdownMenu>
                    </template>
                </Dropdown>
                <Dropdown
                    placement="right-center">
                    <DropdownMenuItem as="button" class="no-ring no no-padding">
                        <div class="flex justify-between items-center px-3 py-1.5" @click="toggleSubmenus('table-column')">
                            Column
                            <IconArrow class="-rotate-90" />
                        </div>
                    </DropdownMenuItem>

                    <template #menu>
                        <DropdownMenu width="160">
                            <div class="table-column"></div>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().addColumnBefore()"
                                @click="runTableCommand('addColumnBefore')">
                                Add column before
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().addColumnAfter()"
                                @click="runTableCommand('addColumnAfter')">
                                Add column after
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().deleteColumn()"
                                @click="runTableCommand('deleteColumn')">
                                Delete column
                            </DropdownMenuItem>
                        </DropdownMenu>
                    </template>
                </Dropdown>
                <Dropdown
                    placement="right-center">
                    <DropdownMenuItem as="button" class="no-ring no no-padding">
                        <div class="flex justify-between items-center px-3 py-1.5" @click="toggleSubmenus('table-cell')">
                            Cell
                            <IconArrow class="-rotate-90" />
                        </div>
                    </DropdownMenuItem>

                    <template #menu>
                        <DropdownMenu width="160">
                            <div class="table-cell"></div>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().mergeCells()"
                                @click="runTableCommand('mergeCells')">
                                Merge cells
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().splitCell()"
                                @click="runTableCommand('splitCell')">
                                Split cell
                            </DropdownMenuItem>
                        </DropdownMenu>
                    </template>
                </Dropdown>
                <Dropdown
                    placement="right-center">
                    <DropdownMenuItem as="button" class="no-ring no no-padding">
                        <div class="flex justify-between items-center px-3 py-1.5" @click="toggleSubmenus('table-heading')">
                            Heading
                            <IconArrow class="-rotate-90" />
                        </div>
                    </DropdownMenuItem>

                    <template #menu>
                        <DropdownMenu width="160">
                            <div class="table-heading"></div>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().toggleHeaderColumn()"
                                @click="runTableCommand('toggleHeaderColumn')">
                                Toggle heading column
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().toggleHeaderRow()"
                                @click="runTableCommand('toggleHeaderRow')">
                                Toggle header row
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                as="button"
                                :disabled="!this.editor.can().toggleHeaderCell()"
                                @click="runTableCommand('toggleHeaderCell')">
                                Toggle header cell
                            </DropdownMenuItem>
                        </DropdownMenu>
                    </template>
                </Dropdown>
                <Dropdown
                    :should-close-on-blur="false"
                    placement="right-center">
                    <DropdownMenuItem as="button" class="no-ring no no-padding">
                        <div class="flex justify-between items-center px-3 py-1.5" @click="toggleSubmenus('table-cell-background-color')">
                            Cell background color
                            <IconArrow class="-rotate-90" />
                        </div>
                    </DropdownMenuItem>

                    <template #menu>
                        <DropdownMenu width="160">
                            <div class="table-cell-background-color"></div>
                            <div class="square-grid">
                                <div
                                    v-for="(color, index) in tableCellBackgroundColors"
                                    :key="`cell-background-color-${index}`"
                                    :style="{ 'background-color': color }"
                                    class="color-option"
                                    @click="setCellBackgroundColor(color)">
                                </div>
                                <div class="custom-color-option">
                                    <tiptap-icon
                                        name="clear-color"
                                        class="custom-color-option-icon"
                                        @click="setCellBackgroundColor('transparent')" />
                                </div>
                                <div class="custom-color-option">
                                    <tiptap-icon
                                        name="swatchbook"
                                        class="custom-color-option-icon"
                                        :style="{ color: currentCellBackgroundColor }"
                                        @click="customCellBackgroundClick" />
                                    <input
                                        type="color"
                                        ref="cellBackgroundColorInput"
                                        @input="setCellBackgroundColor($event.target.value)"
                                        class="custom-color-option-input" />
                                </div>
                            </div>
                        </DropdownMenu>
                    </template>
                </Dropdown>
                <Dropdown
                    :should-close-on-blur="false"
                    placement="right-center">
                    <DropdownMenuItem as="button" class="no-ring no no-padding">
                        <div class="flex justify-between items-center px-3 py-1.5" @click="toggleSubmenus('table-cell-border-color')">
                            Cell border color
                            <IconArrow class="-rotate-90" />
                        </div>
                    </DropdownMenuItem>

                    <template #menu>
                        <DropdownMenu width="160">
                            <div class="table-cell-border-color"></div>
                            <div class="square-grid">
                                <div
                                    v-for="(color, index) in tableCellBorderColors"
                                    :key="`cell-border-color-${index}`"
                                    :style="{ 'background-color': color }"
                                    class="color-option"
                                    @click="setCellBorderColor(color)">
                                </div>
                                <div class="custom-color-option">
                                    <tiptap-icon
                                        name="clear-color"
                                        class="custom-color-option-icon"
                                        @click="setCellBorderColor('transparent')" />
                                </div>
                                <div class="custom-color-option">
                                    <tiptap-icon
                                        name="swatchbook"
                                        class="custom-color-option-icon"
                                        :style="{ color: currentCellBorderColor }"
                                        @click="customCellBorderClick" />
                                    <input
                                        type="color"
                                        ref="cellBorderColorInput"
                                        @input="setCellBorderColor($event.target.value)"
                                        class="custom-color-option-input" />
                                </div>
                            </div>
                        </DropdownMenu>
                    </template>
                </Dropdown>
                <DropdownMenuItem
                    as="button"
                    :disabled="!editor.can().deleteTable()"
                    @click="runTableCommand('deleteTable')">
                    Delete table
                </DropdownMenuItem>
            </DropdownMenu>
        </template>
    </Dropdown>
</template>

<script>
import BaseButton from './BaseButton.vue';
import { TiptapIcon } from '../icons';
import colorUtils from '../../mixins/colorUtils';

export default {
    props: ['editor', 'tableCellBackgroundColors', 'tableCellBorderColors'],
    components: {BaseButton, TiptapIcon},
    mixins:[colorUtils],
    data() {
        return {
            hover: {
                row: 0,
                col: 0,
            },
            colors: ['#000'],
        };
    },
    computed: {
        isActive() {
            return this.editor.isActive('table');
        },
        get currentCellBackgroundColor() {
            if (!this.editor) {
                return '#000000';
            }

            const color = this.editor.getAttributes('tableCell').backgroundColor;

            if (!color) {
                return '#000000';
            } else if (color.startsWith('rgb')) {
                return this.rgbToHex(color);
            }

            return color;
        },
        get currentCellBorderColor() {
            if (!this.editor) {
                return '#000000';
            }

            const color = this.editor.getAttributes('tableCell').borderColor;

            if (!color) {
                return '#000000';
            } else if (color.startsWith('rgb')) {
                return this.rgbToHex(color);
            }

            return color;
        },
    },
    methods: {
        buttonClickMethod() {},
        toggleSubmenus(ignore) {
            this.$nextTick(() => {
                Array.from(document.querySelectorAll('[dusk="dropdown-teleported"]'))
                    .slice(1)
                    .filter((element) => ! element.querySelector(`.${ignore}`))
                    .forEach((element) => {
                        element.querySelector('[dusk="dropdown-overlay"]').click();
                    });
            });
        },
        closeDropdowns() {
            document.querySelectorAll('[dusk="dropdown-overlay"]')
                .forEach((element) => element.click());
        },
        setHover(row, col) {
            this.hover = { row, col };
        },
        insertTable() {
            if (this.hover.row <= 0 && this.hover.col <= 0) {
                return;
            }

            this.editor.commands.insertTable({ rows: this.hover.row, cols: this.hover.col, withHeaderRow: false });

            this.closeDropdowns();
            this.hover = { row: 0, col: 0 };
        },
        runTableCommand(command) {
            const obj = this.editor.chain().focus();

            if (typeof obj[command] === 'function') {
                obj[command]().run();

                this.closeDropdowns();
            }
        },
        setCellBackgroundColor(color) {
            this.editor.chain().focus().setCellAttribute('backgroundColor', color).run();
        },
        customCellBackgroundClick() {
            this.$refs.cellBackgroundColorInput.focus();
            this.$refs.cellBackgroundColorInput.value = this.currentCellBackgroundColor;
            this.$refs.cellBackgroundColorInput.click();
        },
        setCellBorderColor(color) {
            this.editor.chain().focus().setCellAttribute('borderColor', color).run();
        },
        customCellBorderClick() {
            this.$refs.cellBorderColorInput.focus();
            this.$refs.cellBorderColorInput.value = this.currentCellBorderColor;
            this.$refs.cellBorderColorInput.click();
        },
    },
};
</script>

<style lang="scss">
.table-create-square-grid {
    display: grid;
    grid-auto-rows: minmax(0, 1fr);
    grid-template-columns: repeat(10, minmax(0, 1fr));
    gap: 1px;
    padding-bottom: 1px;

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

.-rotate-90 {
    transform: rotate(-90deg);
}

.no-ring:focus {
    box-shadow: none !important;
}

.no-padding {
    padding: 0 !important;
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
