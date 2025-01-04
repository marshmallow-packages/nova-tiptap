let mix = require('laravel-mix');

require('./nova.mix');

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue({ version: 3 })
  .webpackConfig({
    externals: {
      vue: 'Vue',
    },
    output: {
      uniqueName: 'vendor/package',
    },
  })
  .postCss('resources/css/tiptap.css', 'css')
  .nova('marshmallow/tiptap');
