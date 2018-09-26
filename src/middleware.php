<?php
// Application middleware
use Psr7Middlewares\Middleware;
use Psr7Middlewares\Middleware\TrailingSlash;


/**
 * @Middleware Tratamento da / do Request
 * true - Adiciona a / no final da URL
 * false - Remove a / no final da URL
 */
$app->add(new TrailingSlash(false));

// $app->add(new \Slim\Middleware\Minify());

/**
 * Proxys confiáveis
 */
// $trustedProxies = ['0.0.0.0', '127.0.0.1'];
// $app->add(new RKA\Middleware\SchemeAndHost($trustedProxies));
// $app->add(new \Slim\Middleware\Minify($container));


/**
 * Auth básica HTTP
 */
$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    /**
     * Usuários existentes
     */
    "users" => [
      "root" => "toor"
    ],
    /**
     * Blacklist - Deixa todas liberadas e só protege as dentro do array
     */
    "path" => ["/api/db/admin"],
    /**
     * Whitelist - Protege todas as rotas e só libera as de dentro do array
     */
    //"passthrough" => ["/auth/liberada", "/admin/ping"],
  ]));
