<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/en');
Route::redirect('/docs', '/en/docs');

Route::view('/{locale}', 'app')
    ->where('locale', 'en|tr|es|zh|ar|pt|fr');
Route::view('/{locale}/docs', 'app')
    ->where('locale', 'en|tr|es|zh|ar|pt|fr');
