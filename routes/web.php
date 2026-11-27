<?php

use Illuminate\Support\Facades\Route;

$serveVueApp = function () {
    $indexFile = dirname(base_path()) . '/index.html';
    abort_unless(is_file($indexFile), 404);

    return response()->file($indexFile);
};

Route::get('/', $serveVueApp);
Route::fallback($serveVueApp);
