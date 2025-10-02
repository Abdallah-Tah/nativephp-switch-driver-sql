<?php

declare(strict_types=1);

use App\Livewire\DatabaseDemo;
use Illuminate\Support\Facades\Route;

Route::get('/', DatabaseDemo::class)->name('home');
