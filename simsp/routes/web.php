<?php

use Illuminate\Support\Facades\Route;
use App\Models\SparePart;
use App\Models\Machine;

Route::get('/dashboard', function () {
    $categories = SparePart::whereNotNull('category')->distinct()->pluck('category');
    $machines = Machine::all(); // sekalian untuk dropdown mesin di modal nanti
    $spareParts = SparePart::all(); // sekalian untuk dropdown spare part di modal nanti

    return view('dashboard', compact('categories', 'machines', 'spareParts'));
});

Route::get('/', function () {
    return view('welcome');
});
