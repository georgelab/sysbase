<?php
#web routing

$routes = [
    '/admin/test' => [
        'controller'=>'Admin/AdminController',
        'auth'=> true
    ],
    '/inscrito/test' => [
        'controller'=>'Admin/AdminController',
        'auth'=> true
    ],
    '/' => [
        'controller'=>'',
        'auth'=> true
    ]
];
return $routes;