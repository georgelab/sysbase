<?php
/**
 * Application bootstrap
 *
 * @author      George Azevedo <george@fenix.rio.br>
 * @copyright   Copyright (c) 2023 Fênix Comunicação (https://fenix.rio.br)
 */
$bootstrap = [
    'info' => [
        'project' => 'SysBase',
        'golive' => '',
        'devtools' => true,
        'session_name' => 'sysbaseweb',
        'app_key' => 'u&YzNuJ*02btnq0',
        'app_version' => '0001',
        'maintenance' => false,
        'devtoken' => 'devstaff',
        'devtoken_leasetime' => 15 //minutes
    ],
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'sysbase',
        'username' => 'mysql',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => ''
    ],
    'paths' => [
        'root' => str_replace('/app/public', '', $_SERVER['DOCUMENT_ROOT']),
        'application' => str_replace('/app/public', '/app', $_SERVER['DOCUMENT_ROOT']),
        'logs' => '/app/logs',
        'vendor' => '/app/vendor',
        'layout' => '/app/layout',
        'routing'=>'/app/routing',
        'public' => '/app/public',
        'view' => '/app/view',
        'assets' => '/assets',
    ],
    'endpoints' => [
        'http' => '/',
        'api' => '/api'
    ],
    'authorized_requesters' => [
        'localsysbase' => '87a6std9a6sd8fdf5sf',
    ],
    'core_views' => [
        'header' => '_header.php',
        'footer' => '_footer.php',
        '404' => '404.php',
        'maintenance' => 'maintenance.php'
    ],
    'core_controllers' => [
        'default' => '/Kernel/Controllers/Default/DefaultController'
    ],
    'sitemap' => [
        'active' => true,
        'target' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'],
        'cache_file' => $_SERVER['DOCUMENT_ROOT'] . '/assets/_sitemap-cache.log',
    ],
    'seo' => [
        'title' => 'SEO SysBase',
        'description' => 'default description',
        'keywords' => 'default,keywords,to,use',
        'image' => '/assets/image/sysbase-logo.jpg',
        'locale' => 'pt_BR',
        'language' => 'pt-BR',
        'robots' => 'noindex, nofollow'
    ],
    'styles' => [
        'guest' => [
            'frontpage' => [
                '/assets/css/main.3.css'
            ],
            'innerpage' => [
                //'/assets/css/inner.1.css'
                '/assets/css/interna.0.css'
            ],
            'default' => [
                //beware of misusing this section
                '/assets/css/core.1.css',
                '/assets/css/gckconsent.1.0.css'
            ]
        ],
        'auth' => [
            'frontpage' => [
                '/assets/css/main.3.css'
            ],
            'innerpage' => [
                //'/assets/css/inner.1.css'
                '/assets/css/interna.0.css'
            ],
            'default' => [
                //beware of misusing this section
                '/assets/css/core.1.css',
                '/assets/css/gckconsent.1.0.css'
            ]
        ]
    ],
    'scripts' => [
        'guest' => [
            'frontpage' => [
                '/assets/js/home.2.js'
            ],
            'innerpage' => [
                '/assets/js/inner.1.js'
            ],
            'default' => [
                //beware of misusing this section
                '/assets/js/jquery-3.6.0.min.js',
                '/assets/js/core.3.js',
                '/assets/js/gckconsent.1.0.js'
            ]
        ],
        'auth' => [
            'frontpage' => [
                '/assets/js/home.2.js'
            ],
            'innerpage' => [
                '/assets/js/inner.1.js'
            ],
            'default' => [
                //beware of misusing this section
                '/assets/js/jquery-3.6.0.min.js',
                '/assets/js/core.3.js',
                '/assets/js/gckconsent.1.0.js'
            ]
        ]
    ],
    'fonts' => [
        'guest' => [
            'frontpage' => [
                //    '/assets/css/home.1.css'
            ],
            'innerpage' => [
                //    '/assets/css/inner.1.css'
            ],
            'default' => [
                //beware of misusing this section
                '/assets/fonts/Poppins-Light.woff2',
                '/assets/fonts/Poppins-Regular.woff2',
                '/assets/fonts/Poppins-Medium.woff2',
                '/assets/fonts/Poppins-SemiBold.woff2',
                '/assets/fonts/Poppins-Bold.woff2'
            ]
        ],
        'auth' => [
            'frontpage' => [
                //    '/assets/css/home.1.css'
            ],
            'innerpage' => [
                //    '/assets/css/inner.1.css'
            ],
            'default' => [
                //beware of misusing this section
                '/assets/fonts/Poppins-Light.woff2',
                '/assets/fonts/Poppins-Regular.woff2',
                '/assets/fonts/Poppins-Medium.woff2',
                '/assets/fonts/Poppins-SemiBold.woff2',
                '/assets/fonts/Poppins-Bold.woff2'
            ]
        ]
    ],
];
return $bootstrap;
