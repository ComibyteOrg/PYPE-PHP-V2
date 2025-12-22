<?php
use App\Router\Route;

Route::get("/", function () {
    return view("home");
});

Route::socialAuth();