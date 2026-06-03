<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Verificação do Modo de Manutenção
|--------------------------------------------------------------------------
|
| Se a aplicação estiver em modo de manutenção, este arquivo pré-renderizado
| será chamado para que possamos exibir uma mensagem amigável ao usuário.
|
*/
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Registra o Autoloader do Composer
|--------------------------------------------------------------------------
|
| O Composer fornece um carregador de classes gerado automaticamente.
| Nós simplesmente o incluímos aqui para não precisarmos nos preocupar
| em carregar manualmente cada uma de nossas classes no futuro.
|
*/
require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Inicializa o Framework (Bootstrap)
|--------------------------------------------------------------------------
|
| Aqui nós obtemos a instância da aplicação Laravel, que serve como o
| núcleo central do framework, gerenciando middlewares e rotas.
|
*/
$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Executa a Aplicação (Processa a Requisição)
|--------------------------------------------------------------------------
|
| Uma vez que a aplicação foi inicializada, capturamos a requisição HTTP
| vinda do seu PWA, passamos pelo sistema de rotas (api.php) e enviamos
| a resposta JSON de volta para o cliente.
|
*/
$request = Request::capture();
$response = $app->handle($request);

$response->send();

$app->terminate($request, $response);