<?php
// DIC configuration

$container = $app->getContainer();

// Twig
$container['view'] = function ($container) {
  $settings = $container->get('settings')['twig'];
  $view = new \Slim\Views\Twig($settings['path'], [
    'cache' => $settings['cache'],
    'charset' => 'utf-8',
    'autoescape' => true,
    'auto_reload' => true,
    'strict_variables' => false
  ]);
  $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    // Twig Globals
  $view->getEnvironment()->addGlobal('session', $_SESSION);
  return $view;
};

// MarkdownRenderer
$container['markdown'] = function ($container) {
  $settings = $container->get('settings')['markdown'];
  $markdown = new DavidePastore\Slim\Views\MarkdownRenderer($settings['path']);
  return $markdown;
};

// Monolog
$container['logger'] = function ($container) {
  $settings = $container->get('settings')['logger'];
  $logger = new Monolog\Logger($settings['name']);
  $stream = new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG);
  $fingersCrossed = new Monolog\Handler\FingersCrossedHandler($stream, Monolog\Logger::INFO);
  $logger->pushProcessor(new Monolog\Processor\UidProcessor());
  $logger->pushHandler($fingersCrossed);
  return $logger;
};

// Flash Messages
$container['flash'] = function ($container) {
  return new \Slim\Flash\Messages();
};


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
    "path" => ["/api/db/v1/admin"],
    /**
     * Whitelist - Protege todas as rotas e só libera as de dentro do array
     */
    //"passthrough" => ["/auth/liberada", "/admin/ping"],
  ]));

/**
 * Converte os Exceptions Genéricas dentro da Aplicação em respostas JSON
 */
$container['errorHandler'] = function ($container) {
  return function ($request, $response, $exception) use ($container) {
    $statusCode = $exception->getCode() ? $exception->getCode() : 500;
    return $container['response']->withStatus($statusCode)
    ->withHeader('Content-Type', 'Application/json')
    ->withJson(["message" => $exception->getMessage()], $statusCode);
  };
};

/**
 * Converte os Exceptions de Erros 405 - Not Allowed
 */
$container['notAllowedHandler'] = function ($container) {
  return function ($request, $response, $methods) use ($container) {
    return $container['response']
    ->withStatus(405)
    ->withHeader('Allow', implode(', ', $methods))
    ->withHeader('Content-Type', 'Application/json')
    ->withHeader("Access-Control-Allow-Methods", implode(",", $methods))
    ->withJson(["message" => "Method not Allowed; Method must be one of: " . implode(', ', $methods)], 405);
  };
};

/**
 * Converte os Exceptions de Erros 404 - Not Found
 */
$container['notFoundHandler'] = function ($container) {
  return function ($request, $response) use ($container) {
    return $container['response']
    ->withStatus(404)
    ->withHeader('Content-Type', 'Application/json')
    ->withJson(['message' => 'Page not found'], 200, JSON_PRETTY_PRINT);
  };
};


/**
 * Proxys confiáveis
 */
$trustedProxies = ['0.0.0.0', '127.0.0.1'];
// $app->add(new RKA\Middleware\SchemeAndHost($trustedProxies));
