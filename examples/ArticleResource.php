<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;
use Marshmallow\Tiptap\Tiptap;

class Article extends Resource
{
    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields($request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->rules('required', 'max:255'),

            // Example 1: Enable image pruning globally via config
            Tiptap::make('Content')
                ->buttons(['heading', 'bold', 'italic', 'image', 'link'])
                ->imageSettings([
                    'disk' => 'public',
                    'path' => 'articles/images',
                ]),

            // Example 2: Enable image pruning specifically for this field
            Tiptap::make('Description')
                ->buttons(['bold', 'italic', 'image'])
                ->imageSettings([
                    'disk' => 'public',
                    'path' => 'articles/descriptions',
                ])
                ->pruneImages(), // This enables pruning for this specific field

            // Example 3: Multiple content fields with different settings
            Tiptap::make('Summary')
                ->buttons(['bold', 'italic'])
                ->imageSettings([
                    'disk' => 's3', // Different disk
                    'path' => 'summaries',
                ])
                ->pruneImages(true), // Explicit boolean parameter
        ];
    }
}
