export default {
    methods: {
        sanitizeTiptapContent(value) {
            // Only sanitize if the option is enabled in the field configuration
            const shouldSanitize =
                (this.field && this.field.sanitizeEmptyContent) ||
                (this.currentField && this.currentField.sanitizeEmptyContent);

            // Check for empty paragraphs that should be converted to empty strings
            if (
                shouldSanitize &&
                (value === "<p></p>" ||
                    value === '<p style="text-align: left"></p>' ||
                    value === '<p style="text-align: left;"></p>')
            ) {
                return "";
            }
            return value;
        },
    },
};
