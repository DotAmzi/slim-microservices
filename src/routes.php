<?php
// Routes
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/index', function (Request $request, Response $response, array $args) {
  $options = array(
    'title' => 'Conheça minha api desenvolvida com SlimFramework',
    'link' => '.',
    'links' => array(
      '1' => ['db', '/db/docs'],
      '2' => ['index', '/index']
    )
  );
  $this->view->offsetSet("options", $options);
  return $this->view->render($response, 'slim-default.html', $args);
});


$app->get('/md', function (Request $request, Response $response, array $args) {
  $options = array(
    'title' => 'Conheça minha api desenvolvida com SlimFramework',
    'link' => '.',
    'links' => array(
      '1' => ['db', '/db/docs'],
      '2' => ['index', '/index']
    )
  );
  $this->view->offsetSet("options", $options);
  return $this->markdown->render($response, 'hello.md', $args);
});


$app->get('/docs','DatabaseController:docs');
$app->get('/','DatabaseController:index');

/**
  * Grupo dos enpoints iniciados por v1
  */
$app->group('/{database}',function() {

    $this->get('','DatabaseController:list');
    $this->post('','DatabaseController:create');

    $this->get('/{id:[0-9]+}', 'DatabaseController:view');
    $this->put('/{id:[0-9]+}', 'DatabaseController:update');
    $this->delete('/{id:[0-9]+}', 'DatabaseController:delete');

});
// })->add(new AuthMiddleware($container));
