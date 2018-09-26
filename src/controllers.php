<?php
// Application controllers

$container['DatabaseAdminController'] = function($container){
  return new \App\Controllers\DatabaseAdminController($container);
};

$container['DatabaseController'] = function($container){
  return new \App\Controllers\DatabaseController($container);
};
