<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return  view('welcome'); //['Laravel' => app()->version()];
});
Route::get('/docs', function () {
    return view('docs.index');
});

require __DIR__.'/auth.php';
