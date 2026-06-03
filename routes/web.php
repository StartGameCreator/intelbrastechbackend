<?php

use Illuminate\Support\Facades\Route;

// Rota padrão para quem acessar a raiz pelo navegador
Route::get('/', function () {
    return response()->json(['message' => 'IntelbrasTech API está online!']);
});