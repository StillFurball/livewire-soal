<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

// Setiap halaman hanya membungkus satu komponen Livewire.
Route::view('/soal-1', 'soal.1')->name('soal1');
Route::view('/soal-2', 'soal.2')->name('soal2');
Route::view('/soal-3', 'soal.3')->name('soal3');
Route::view('/soal-4', 'soal.4')->name('soal4');
Route::view('/soal-5', 'soal.5')->name('soal5');
