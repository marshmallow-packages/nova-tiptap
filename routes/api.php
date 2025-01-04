<?php

use Illuminate\Support\Facades\Route;
use Marshmallow\Tiptap\Controllers\FilesController;
use Marshmallow\Tiptap\Controllers\ImagesController;

Route::post('file', FilesController::class . '@store');
Route::post('image', ImagesController::class . '@store');
