<?php

use Diagro\Backend\Middleware\BackendAuthentication;
use Illuminate\Support\Facades\Route;

Route::middleware(BackendAuthentication::class)->prefix('_')->group(function() {



});
