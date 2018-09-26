<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class DatabaseController extends Controller {

  public function index(Request $request, Response $response, array $args) {

    $this->flash->addMessageNow('error', 'Mensagem de erro :(');
    $this->flash->addMessageNow('success', 'Mensagem de sucesso :)');
    $messages = $this->flash->getMessages();

    // $this->logger->info("Slimsn '/' slim-demo");

    /** Lista os diretorios da pasta /var/www/ */
    // $html = "";

    $options = array(
      'title' => 'PHP JSON Server',
      'link' => '/api/db/docs',

      "module" => "docs"
    );

    $dir = __DIR__ . "/../../database/";
    $items = glob("$dir{*.*, *.json}", GLOB_BRACE);

    $items = str_replace($dir,'',  $items);

    $this->view->offsetSet("dir", $dir);
    $this->view->offsetSet("items", $items);


    $this->view->offsetSet("options", $options);


    return $this->view->render($response, 'home/index.html', $args);


  }
  public function docs(Request $request, Response $response, array $args) {

    $this->flash->addMessageNow('error', 'Mensagem de erro :(');
    $this->flash->addMessageNow('success', 'Mensagem de sucesso :)');
    $messages = $this->flash->getMessages();


      // $this->logger->info("Slimsn '/' slim-demo");

    /** Lista os diretorios da pasta /var/www/ */
      // $html = "";

    $dir = __DIR__ . "/../../database/";
    $items = glob("$dir{*.*, *.json}", GLOB_BRACE);

    $items = str_replace($dir,'',  $items);

    $this->view->offsetSet("dir", $dir);
    $this->view->offsetSet("items", $items);


    $this->logger->info("Slim-Skeleton '/' slim-demo");

    $options = array(
      'title' => 'Conheça minha api desenvolvida com SlimFramework',
      'link' => '/api/db/docs',

      "module" => "docs"
    );
    $this->view->offsetSet("options", $options);

    return $this->view->render($response, 'docs.html', $args);


  }


  private function findById($vector, $param1){
    $find = -1;
    foreach($vector as $key => $obj){
      if($obj['id'] == $param1){
        $find = $key;
        break;
      }
    }
    return $find;
  }

  private function formatDate() {
    $now = date_create('now')->format('Y-m-d H:i:s');
    return $now;
  }

  private function ckeckDatabase($db) {
    if (!file_exists($db)) {
      return false;
    } else {
      return true;
    }
  }

  private function saveDatabase($file, $data){
    $f = @fopen($file, 'w');
    if (!$f) {
      return false;
    } else {
      $bytes = fwrite($f, $data);
      fclose($f);
      return $bytes;
    }
  }

  /**
   * Listagem do banco de dados.
   */
  public function list(Request $request, Response $response, array $args) {

    $db = __DIR__ . '/../../database/'.$args['database'].'.json';
    $table = strtolower($args['database']);

    if($this->ckeckDatabase($db) == true) {
      $contents = file_get_contents($db);
      $json = json_decode($contents, true);
      return $response->withJson($json[$table], 200, JSON_PRETTY_PRINT);
    } else {
      return $response->withJson(['message' => 'A base de dados nao existe!'], 200, JSON_PRETTY_PRINT);
    };

  }

  /**
    * Cria um registro no banco de dados.
    */
  public function create(Request $request, Response $response, array $args) {

    $db = __DIR__ . '/../../database/'.$args['database'].'.json';
    $table = strtolower($args['database']);

    if($this->ckeckDatabase($db) == true) {
      $contents = file_get_contents($db);
      $body = file_get_contents('php://input');
      $jsonBody = json_decode($body, true);
      $jsonBody['date'] = $this->formatDate();
      $jsonBody['id'] = time();
      $json = json_decode($contents, true);
      $json[$table][] = $jsonBody;
      $ret = ['message' => $this->saveDatabase($db, json_encode($json, JSON_PRETTY_PRINT))];
      return $response->withJson($ret, 200, JSON_PRETTY_PRINT);
    } else {
      return $response->withJson(['message' => 'A base de dados nao existe!'], 200, JSON_PRETTY_PRINT);
    };

  }

  public function view(Request $request, Response $response, array $args) {

    $db = __DIR__ . '/../../database/'.$args['database'].'.json';
    $table = strtolower($args['database']);

    if($this->ckeckDatabase($db) == true) {
      $contents = file_get_contents($db);
      $id = $args['id'];
      $json = json_decode($contents, true);
      $ret = $this->findById($json[$table], $id);
      if($ret != -1){
        $ret = $json[$table][$ret];
      } else {
        $ret = ['message' => 'O registro nao existe!'];
      }
      return $response->withJson($ret, 200, JSON_PRETTY_PRINT);
    } else {
      return $response->withJson(['message' => 'A base de dados nao existe!'], 200, JSON_PRETTY_PRINT);
    };

  }

  public function update(Request $request, Response $response, array $args) {

    $db = __DIR__ . '/../../database/'.$args['database'].'.json';
    $table = strtolower($args['database']);

    if($this->ckeckDatabase($db) == true) {
      $contents = file_get_contents($db);
      $id = $args['id'];
      $json = json_decode($contents, true);
      $ret = $this->findById($json[$table], $id);
      if($ret != -1){
        $body = file_get_contents('php://input');
        $jsonBody = json_decode($body, true);
        $jsonBody['date'] = $this->formatDate();
        $jsonBody['id'] = $id;
        $json[$table][$ret] = $jsonBody;
        $ret = ['message' => $this->saveDatabase($db, json_encode($json, JSON_PRETTY_PRINT))];
      } else {
        $ret = ['message' => 'O registro nao existe!'];
      }
      return $response->withJson($ret, 200, JSON_PRETTY_PRINT);
    } else {
      return $response->withJson(['message' => 'A base de dados nao existe!'], 200, JSON_PRETTY_PRINT);
    };

  }

  public function delete(Request $request, Response $response, array $args) {

    $db = __DIR__ . '/../../database/'.$args['database'].'.json';
    $table = strtolower($args['database']);

    if($this->ckeckDatabase($db) == true) {
      $contents = file_get_contents($db);
      $id = (int) $args['id'];
      $json = json_decode($contents, true);
      $ret = $this->findById($json[$table], $id);
      if($ret != -1){
      // $body = file_get_contents('php://input');
      // $jsonBody = json_decode($body, true);
      // $jsonBody['id'] = $ret;
        unset($json[$table][$ret]);
      //unset($json[$table][$ret]);
        $ret = ['message' => $this->saveDatabase($db, json_encode($json, JSON_PRETTY_PRINT))];
      } else {
        $ret = ['message' => 'O registro nao existe!'];
      }
      return $response->withJson($ret, 200, JSON_PRETTY_PRINT);
    } else {
      return $response->withJson(['message' => 'A base de dados nao existe!'], 200, JSON_PRETTY_PRINT);
    };

  }



}
