<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Criação da Aplicação (O Motor do Framework)
|--------------------------------------------------------------------------
|
| Aqui nós configuramos os caminhos base do projeto e inicializamos
| a instância principal do Laravel responsável por gerenciar toda a API.
|
*/
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aqui o Laravel 11 gerencia os middlewares globais
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tratamento global de erros e exceções da API
    })->create();