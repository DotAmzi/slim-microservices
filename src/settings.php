<?php

// Settings

return [
  'settings' => [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => true, // Allow the web server to send the content-length header
    'determineRouteBeforeAppMiddleware' => true,
    'debug' => true,

    // Twig settings
    'twig' => [
      'path' => __DIR__ . '/../templates/',
      'cache' => __DIR__ . '/../cache/',
    ],

    // Twig settings
    'markdown' => [
      'path' => __DIR__ . '/../templates/'
    ],

    // Monolog settings
    'logger' => [
      'name' => 'php-json-server',
      'path' => __DIR__ . '/../logs/app.log'
    ],

  ],
];
