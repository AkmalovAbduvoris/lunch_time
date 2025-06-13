<?php

use Illuminate\Support\Facades\Route;
use DefStudio\Telegraph\Facades\Telegraph;

Route::get('/', function () {
    return view('welcome');
});
