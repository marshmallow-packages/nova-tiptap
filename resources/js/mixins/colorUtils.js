export default {
    methods: {
        rgbToHex(rgb) {
            const rgbArray = rgb.match(/\d+/g);

            const hexColor = rgbArray
                .map((value) => parseInt(value).toString(16).padStart(2, '0'))
                .join('');

            return `#${hexColor}`;
        },
    },
};