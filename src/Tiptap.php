<?php

namespace Marshmallow\Tiptap;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Expandable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\SupportsDependentFields;
use Marshmallow\Tiptap\Services\ImagePruningService;

class Tiptap extends Field
{
    use Expandable, SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'tiptap';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Indicates if the field should save it's content as json object.
     *
     * @var bool
     */
    public $saveAsJson = false;

    /**
     * Set the buttons that should be available in the menu.
     *
     * @param  array  $buttons
     * @return $this
     */
    public function buttons(array $buttons)
    {
        return $this->withMeta([
            'buttons' => $buttons,
        ]);
    }

    /**
     * Set the heading levels that should be available when using headings.
     *
     * @param  mixed  $headingLevels
     * @return $this
     */
    public function headingLevels($headingLevels)
    {
        $headingLevelsArr = $headingLevels;
        if (is_int($headingLevels)) {
            $headingLevelsArr = [];
            for ($n = 1; $n <= $headingLevels; $n++) {
                $headingLevelsArr[] = $n;
            }
        }

        return $this->withMeta([
            'headingLevels' => $headingLevelsArr,
        ]);
    }

    /**
     * Set the colors that should be available when using color selector.
     *
     * @param array $colors
     * @return $this
     */
    public function colors($colors)
    {
        return $this->withMeta([
            'colors' => $colors,
        ]);
    }

    /**
     * Set the colors that should be available when using background color selector.
     *
     * @param array $colors
     * @return $this
     */
    public function backgroundColors($colors)
    {
        return $this->withMeta([
            'backgroundColors' => $colors,
        ]);
    }

    /**
     * Set the colors that should be available when using table cell background color selector.
     *
     * @param array $colors
     * @return $this
     */
    public function tableCellBackgroundColors($colors)
    {
        return $this->withMeta([
            'tableCellBackgroundColors' => $colors,
        ]);
    }

    /**
     * Set the colors that should be available when using table cell border color selector.
     *
     * @param array $colors
     * @return $this
     */
    public function tableCellBorderColors($colors)
    {
        return $this->withMeta([
            'tableCellBorderColors' => $colors,
        ]);
    }

    /**
     * Turn on syntax highlighting when using code_block.
     *
     * @param  mixed  $syntaxHighlighting
     * @return $this
     */
    public function syntaxHighlighting()
    {
        return $this->withMeta([
            'syntaxHighlighting' => true,
        ]);
    }

    /**
     * Set the heading levels that should be available when using headings.
     *
     * @param  mixed  $htmlTheme
     * @return $this
     */
    public function htmlTheme($htmlTheme)
    {
        return $this->withMeta([
            'htmlTheme' => $htmlTheme,
        ]);
    }

    /**
     * Set the alignments that should be available when using textAlign.
     *
     * @param  mixed  $alignments
     * @return $this
     */
    public function alignments($alignments)
    {
        $alignments = array_map(function ($item) {
            if ($item == 'left') {
                return 'start';
            }
            if ($item == 'right') {
                return 'end';
            }

            return $item;
        }, $alignments);

        return $this->withMeta([
            'alignments' => $alignments,
        ]);
    }

    /**
     * Set the DOM elements that can be aligned when using textAlign.
     *
     * @param  mixed  $alignElements
     * @return $this
     */
    public function alignElements($alignElements)
    {
        return $this->withMeta([
            'alignElements' => $alignElements,
        ]);
    }

    /**
     * Set the default alignment when using textAlign.
     *
     * @param  mixed  $defaultAlignment
     * @return $this
     */
    public function defaultAlignment($defaultAlignment)
    {
        return $this->withMeta([
            'defaultAlignment' => $defaultAlignment,
        ]);
    }

    /**
     * Set the placeholder.
     *
     * @param  mixed  $placeholder
     * @return $this
     */
    public function placeholder($placeholder)
    {
        return $this->withMeta([
            'placeholder' => $placeholder,
        ]);
    }

    /**
     * Set the disk and the path for images.
     *
     * @param  mixed  $imageSettings
     * @return $this
     */
    public function imageSettings($imageSettings)
    {
        return $this->withMeta([
            'imageSettings' => $imageSettings,
        ]);
    }

    /**
     * Set the disk and the path for files.
     *
     * @param  mixed  $fileSettings
     * @return $this
     */
    public function fileSettings($fileSettings)
    {
        return $this->withMeta([
            'fileSettings' => $fileSettings,
        ]);
    }

    /**
     * Set the disk and the path for linked files.
     *
     * @param  mixed  $linkSettings
     * @return $this
     */
    public function linkSettings($linkSettings)
    {
        return $this->withMeta([
            'linkSettings' => $linkSettings,
        ]);
    }

    /**
     * Set the placeholder blocks array.
     *
     * @param  mixed  $placeholderBlocks
     * @return $this
     */
    public function placeholderBlocks($placeholderBlocks)
    {
        return $this->withMeta([
            'placeholderBlocks' => $placeholderBlocks,
        ]);
    }

    /**
     * Set setting to save the input as json object.
     *
     * @param  bool  $saveAsJson
     * @return $this
     */
    public function saveAsJson()
    {
        return $this->withMeta([
            'saveAsJson' => true,
        ]);
    }

    /**
     * Set sanitize option to clean empty content.
     *
     * @param  bool  $sanitizeEmptyContent
     * @return $this
     */
    public function sanitizeEmptyContent()
    {
        return $this->withMeta([
            'sanitizeEmptyContent' => true,
        ]);
    }

    /**
     * Enable automatic pruning of uploaded images when they are removed from content.
     *
     * @param  bool  $enabled
     * @return $this
     */
    public function pruneImages($enabled = true)
    {
        return $this->withMeta([
            'pruneImages' => $enabled,
        ]);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        // Get the old value before updating
        $oldValue = $model->{$attribute} ?? null;

        // Fill the attribute with the new value
        parent::fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);

        // Get the new value after updating
        $newValue = $model->{$attribute} ?? null;


        // Prune images if enabled
        $this->pruneImagesIfEnabled($oldValue, $newValue);
    }

    /**
     * Prune images if the feature is enabled.
     *
     * @param  string|null  $oldContent
     * @param  string|null  $newContent
     * @return void
     */
    protected function pruneImagesIfEnabled($oldContent, $newContent)
    {
        // Check if pruning is enabled globally or for this field
        $globallyEnabled = config('nova-tiptap.prune_images', false);
        $fieldEnabled = Arr::get($this->meta, 'pruneImages');

        if ($fieldEnabled === false) {
            return;
        }

        if ($globallyEnabled === false && !$fieldEnabled) {
            return;
        }

        // Get image settings for this field
        $imageSettings = $this->meta['imageSettings'] ?? [];

        // Perform the pruning
        ImagePruningService::pruneImages($oldContent, $newContent, $imageSettings);
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'shouldShow' => $this->shouldBeExpanded(),
            'contentBlocks' => [
                [
                    'key' => 'video',
                    'title' => 'Video',
                ],
                [
                    'key' => 'gallery',
                    'title' => 'Gallery',
                ],
            ],
        ]);
    }
}
