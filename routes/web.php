<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/todos', function () {
    return view('todos.manage');
})->name('todos.manage');

Route::get('/people', function () {
    return view('people.manage');
})->name('people.manage');