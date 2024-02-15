<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Helpers;

use App\Kernel\Helpers\{Router};
use App\Kernel\Controllers\Controller;

/**
 * Class Render
 */
class Render
{

    /**
     * Default variables
     */
    public $appSetup = [];

    public $Router = [];


    /**
     * Router constructor
     */
    public function __construct()
    {
        $this->Router = new Router();
        $this->appSetup = json_decode(CoreSetup);
    }

    /**
     * protectFromDirectRender
     */
    public function protectFromDirectRender(): void
    {
        $file_caller = (
            debug_backtrace()[0]
            && debug_backtrace()[0]['file']
        ) ? basename(debug_backtrace()[0]['file']) : '';

        if (
            $file_caller
            && strpos(
                $_SERVER['REQUEST_URI'],
                $file_caller
            ) !== false
        ) {
            Router::toURL('/');
            exit;
        }
    }

    /**
     * setMaintenanceMode
     * 
     * @param bool $status
     */
    public function setMaintenanceMode($status = false)
    {
        $this->appSetup->info->maintenance = $status;
    }

    /**
     * getMaintenanceMode
     * 
     * @param bool $status
     */
    public function getMaintenanceMode()
    {
        return $this->appSetup->info->maintenance;
    }


    /**
     * display
     * 
     * @param object $destination
     */
    public function display(object $destination)
    {

        $maintenance_mode = (
            $this->getMaintenanceMode() === false
            || ($this->getMaintenanceMode() === true && isset($_COOKIE[$this->appSetup->info->devtoken]))
        ) ? false : true;

        $view = $this->appSetup->paths->root . $this->appSetup->paths->view;
        $content = $view . '/' . $destination->file;

        if ($destination->file == $this->appSetup->core_views->{'404'})
            http_response_code(404);

        $this->seo = $this->getSEOInfo($content);

        $webrouting = require($this->appSetup->paths->root . $this->appSetup->paths->routing . "/web.php");

        if (
            array_key_exists('/' . $destination->link, $webrouting)
            && isset($webrouting['/' . $destination->link]['controller'])
            && $webrouting['/' . $destination->link]['controller'] != ''
        ) {
            $toload = $this->appSetup->paths->application . "/Kernel/Controllers/" . $webrouting['/' . $destination->link]['controller'] . '.php';
            if (file_exists($toload)) {
                require($toload);
                $classname = '\App\Kernel\Controllers\\' . str_replace('/', '\\', $webrouting['/' . $destination->link]['controller']);
                $controller = new $classname;
            }
        } else {
            require ($this->appSetup->paths->application . $this->appSetup->core_controllers->default . '.php');
            $classname = str_replace('/', '\\', '/App'. $this->appSetup->core_controllers->default);
            $controller = new $classname;
        } 

        if (isset($destination->params))
            $_params = $destination->params;

        if (!$maintenance_mode) {
            $layout = $this->appSetup->paths->root . $this->appSetup->paths->layout;
            include($layout . '/' . $destination->layout . '.php');
        } else {
            include($view . '/' . $this->appSetup->core_views->maintenance);
        }


    }

    /**
     * slugToTitle
     * 
     * @param string $slug
     */
    public function slugToTitle($slug = '')
    {
        $title = '';
        if (!empty($slug)) {
            if (isset($this->appSetup->special_diacritics->{$slug})) {
                $title = ucwords(str_replace('-', ' ', $this->appSetup->special_diacritics->{$slug}));
            } else {
                $title = ucwords(str_replace('-', ' ', $slug));
            }
        }
        return $title;
    }

    /**
     * getViewHierarchy
     * 
     * @param bool $print
     * @param string $separator
     */
    public function getViewHierarchy($print = false, $separator = '')
    {
        $view_fullpath = $this->appSetup->paths->root . $this->appSetup->paths->view;
        $reverse_hierarchy = array_reverse(explode('/', $this->Router->current()->link));
        $map = [];

        foreach ($reverse_hierarchy as $k => $current) {
            $parents = explode($current, $this->Router->current()->link)[0];
            if (file_exists($view_fullpath . '/' . $parents . $current . '.php') || file_exists($view_fullpath . '/' . $parents . '/' . $current . '/index.php')) {
                $filepath = (file_exists($view_fullpath . '/' . $parents . $current . '.php'))
                    ? $view_fullpath . '/' . $parents . $current . '.php'
                    : $view_fullpath . '/' . $parents . '/' . $current . '/index.php';
                $seoinfo = $this->getSEOInfo($filepath);
                $active = ($k == 0) ? true : false;
                $map[] = ['title' => $seoinfo->title, 'path' => '/' . $parents . $current, 'active' => $active];
            } else {
                $map[] = ['title' => $this->slugToTitle($current), 'path' => '', 'active' => false];
            }
        }
        $map[] = ['title' => 'Home', 'path' => '/', 'active' => false];
        $map = array_reverse($map);

        if ($print) {
            $printable = [];
            foreach ($map as $item) {
                if (!empty($item['path']) && $item['active'] !== true) {
                    $printable[] = '<a href="' . $item['path'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a>';
                } else {
                    $printable[] = ($item['active']) ? '<span class="active">' . $item['title'] . '</span>' : '<span>' . $item['title'] . '</span>';
                }
            }
            return (!empty($separator)) ? implode('<span>' . $separator . '</span>', $printable) : implode('', $printable);
        } else {
            return $map;
        }

    }

    /**
     * loadScripts
     */
    public function loadScripts($preload = '')
    {
        $scripts_to_add = '';
        $type = ($this->Router->current()->link == '') ? 'frontpage' : 'innerpage';

        if (isset($this->appSetup->scripts)) {
            if (isset($this->appSetup->scripts->default)) {
                foreach ($this->appSetup->scripts->default as $new_script) {
                    if (file_exists($this->appSetup->paths->root . $this->appSetup->paths->public . $new_script)) {
                        if ($preload === 'preload') {
                            $scripts_to_add .= PHP_EOL . "\t" . '<link rel="preload" href="' . $new_script . '?' . $this->appSetup->info->app_key . '" as="script" />';
                        } else {
                            $scripts_to_add .= PHP_EOL . '<script src="' . $new_script . '?' . $this->appSetup->info->app_key . '"></script>';
                        }
                    }
                }
            }
            if (isset($this->appSetup->scripts->{$type})) {
                foreach ($this->appSetup->scripts->{$type} as $new_script) {
                    if (file_exists($this->appSetup->paths->root . $this->appSetup->paths->public . $new_script)) {
                        if ($preload === 'preload') {
                            $scripts_to_add .= PHP_EOL . "\t" . '<link rel="preload" href="' . $new_script . '?' . $this->appSetup->info->app_key . '" as="script" />';
                        } else {
                            $scripts_to_add .= PHP_EOL . '<script src="' . $new_script . '?' . $this->appSetup->info->app_key . '"></script>';
                        }
                    }
                }
            }
        }

        echo $scripts_to_add . PHP_EOL;
    }

    /**
     * loadFonts
     */
    public function loadFonts($preload = '')
    {
        $fonts_to_add = '';
        $type = ($this->Router->current()->link == '') ? 'frontpage' : 'innerpage';

        if (isset($this->appSetup->fonts)) {
            if (isset($this->appSetup->fonts->default)) {
                foreach ($this->appSetup->fonts->default as $new_font) {
                    if (file_exists($this->appSetup->paths->root . $this->appSetup->paths->public . $new_font)) {
                        if ($preload === 'preload') {
                            $fonts_to_add .= PHP_EOL . "\t" . '<link rel="preload" href="' . $new_font . '?' . $this->appSetup->info->app_key . '" as="font" type="font/woff2" crossorigin />';
                        } else {
                            $fonts_to_add .= PHP_EOL . '<script src="' . $new_font . '?' . $this->appSetup->info->app_key . '"></script>';
                        }
                    }
                }
            }
            if (isset($this->appSetup->fonts->{$type})) {
                foreach ($this->appSetup->fonts->{$type} as $new_font) {
                    if (file_exists($this->appSetup->paths->root . $this->appSetup->paths->public . $new_font)) {
                        if ($preload === 'preload') {
                            $fonts_to_add .= PHP_EOL . "\t" . '<link rel="preload" href="' . $new_font . '?' . $this->appSetup->info->app_key . '" as="font" type="font/woff2" crossorigin />';
                        } else {
                            $fonts_to_add .= PHP_EOL . '<script src="' . $new_font . '?' . $this->appSetup->info->app_key . '"></script>';
                        }
                    }
                }
            }
        }

        echo $fonts_to_add . PHP_EOL;
    }

    /**
     * loadStyles
     */
    public function loadStyles($prefetch = '')
    {
        $styles_to_add = '';
        $type = ($this->Router->current()->link == '') ? 'frontpage' : 'innerpage';

        if (isset($this->appSetup->styles)) {
            if (isset($this->appSetup->styles->default)) {
                foreach ($this->appSetup->styles->default as $new_stylesheet) {
                    if (file_exists($this->appSetup->paths->root . $this->appSetup->paths->public . $new_stylesheet)) {
                        if ($prefetch === 'prefetch') {
                            $styles_to_add .= PHP_EOL . "\t" . '<link rel="prefetch" href="' . $new_stylesheet . '?' . $this->appSetup->info->app_key . '" as="style">';
                        } else {
                            $styles_to_add .= PHP_EOL . '<link href="' . $new_stylesheet . '?' . $this->appSetup->info->app_key . '" rel="stylesheet" media="none" onload="if(media!=\'all\')media=\'all\'"/>';
                        }
                    }
                }
            }
            if (isset($this->appSetup->styles->{$type})) {
                foreach ($this->appSetup->styles->{$type} as $new_stylesheet) {
                    if (file_exists($this->appSetup->paths->root . $this->appSetup->paths->public . $new_stylesheet)) {
                        if ($prefetch === 'prefetch') {
                            $styles_to_add .= PHP_EOL . "\t" . '<link rel="prefetch" href="' . $new_stylesheet . '?' . $this->appSetup->info->app_key . '" as="style">';
                        } else {
                            $styles_to_add .= PHP_EOL . '<link href="' . $new_stylesheet . '?' . $this->appSetup->info->app_key . '" rel="stylesheet" media="none" onload="if(media!=\'all\')media=\'all\'"/>';
                        }
                    }
                }
            }
        }

        echo $styles_to_add . PHP_EOL;
    }


    /**
     * developmentTools
     * 
     * @param bool $print
     */
    public function developmentTools($print = false)
    {
        if ($this->appSetup->info->devtools === true) {
            $dev_info = [
                'PHP_SESSION' => $_SESSION ?? [],
                //'PHP_WARNS' => $this->warnings ?? [],
                'PHP_SERVER' => $_SERVER ?? [],
                'PHP_REQUESTS' => $_REQUEST ?? [],
                'PHP_COOKIES' => $_COOKIE ?? []
            ];
            $dev_info = json_encode($dev_info, JSON_FORCE_OBJECT);
            if ($print) {
                echo PHP_EOL . '<script>console.log(' . $dev_info . ');</script>';
            } else {
                return '<script>console.log(' . $dev_info . ');</script>';
            }
        }
    }

    /**
     * getSchemaOrg
     * 
     * @param bool $print
     * @param array $addinfo 
     */
    public function getSchemaOrg(bool $print = false, array $addinfo = [])
    {
        $nav = $this->Router->current();
        $main_URL = rtrim($this->Router->getURL(), '/');
        $seo_info = $this->getSEOInfo($nav->file) ?? [];

        $mainSchema = [];

        $s_head = '{
			"@context":"https://schema.org",
			"@graph":[';

        $s_def_organization = '{
			"@type":"Organization",
			"@id":"' . $main_URL . '#organization",
			"name":"' . $this->appSetup->info->project . '",
			"url":"' . $main_URL . '",
			"sameAs":["' . $main_URL . '/"],
			"logo":{
				"@type":"ImageObject",
				"@id":"' . $main_URL . '#logo",
				"inLanguage":"en-US",
				"url":"' . $main_URL . '/assets/image/og_logo.jpg",
				"contentUrl":"' . $main_URL . '/assets/image/og_logo.jpg",
				"width":1200,
				"height":627,
				"caption":"' . $this->appSetup->info->project . '"
			},
			"image":{
				"@id":"' . $main_URL . '#logo"
			}
		}';

        $s_def_website = '{
			"@type":"WebSite",
			"@id":"' . $main_URL . '#website",
			"url":"' . $main_URL . '",
			"name":"' . $this->appSetup->info->project . ' Website",
			"description":"' . $this->appSetup->info->project . ' description",
			"publisher":{
				"@id":"' . $main_URL . '#organization"
			},
			"inLanguage":"pt-BR"
		}';

        //images
        $imgpath = explode('/assets', $seo_info->image);
        $locpath = $this->appSetup->paths->root . $this->appSetup->paths->public . '/' . 'assets' . $imgpath[1];
        list($iwidth, $iheight) = getimagesize($locpath);

        $s_def_image = '{
			"@type":"ImageObject",
			"@id":"' . $this->Router->getURL(true) . '#' . md5($seo_info->image) . '",
			"inLanguage":"en-US",
			"url":"' . $seo_info->image . '",
			"contentUrl":"' . $seo_info->image . '",
			"width":' . $iwidth . ',
			"height":' . $iheight . ',
			"caption":"' . $seo_info->title . '"
		}';


        $webpageid = 'home';
        if ($_SERVER['REQUEST_URI'] != '/') {
            $webpageid = str_replace('/', '-', ltrim($_SERVER['REQUEST_URI'], '/'));
        }
        $wpbreads = ($nav->file != 'frontpage.php') ? '"breadcrumb":{"@id":"' . $this->Router->getURL(true) . '#breadcrumb"},' : '';

        $s_def_webpage = '{
			"@type":"WebPage",
			"@id":"' . $this->Router->getURL(true) . '#' . $webpageid . '",
			"url":"' . $this->Router->getURL(true) . '",
			"name":"' . $seo_info->title . '",
			"isPartOf":{"@id":"' . $this->Router->getURL() . '#website"},
			"primaryImageOfPage":{"@id":"' . $this->Router->getURL(true) . '#' . md5($seo_info->image) . '"},
			"description":"' . $seo_info->description . '",
			' . $wpbreads . '
			"inLanguage":"en-US",
			"potentialAction":[{
				"@type":"ReadAction",
				"target":["' . $this->Router->getURL(true) . '"]
			}]
		}';

        $s_def_author = '{
			"@type":"Person",
			"@id":"' . $main_URL . '#/schema/person/' . mb_strtolower($this->appSetup->info->project) . '",
			"name":"' . $this->appSetup->info->project . ' team",
			"description":"Content authority",
			"url":"' . $main_URL . '"
        }';

        $s_def_breads2 = [
            "@type" => "BreadcrumbList",
            "@id" => $this->Router->getURL(true) . '#breadcrumb',
            "itemListElement" => [
                [
                    "@type" => "ListItem",
                    "position" => 1,
                    "name" => "Home",
                    "item" => $this->Router->getURL()
                ]
            ]
        ];

        $s_def_breads = json_encode($s_def_breads2, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $article_keywords = [];
        foreach (explode(',', $seo_info->keywords) as $key) {
            array_push($article_keywords, '"' . $key . '"');
        }

        $s_def_article = '{
            "@type":"Article",
            "@id":"' . $this->Router->getURL(true) . '#article",
            "isPartOf":{"@id":"' . $this->Router->getURL(true) . '#' . $webpageid . '"},
            "author":{"@id":"' . $main_URL . '#/schema/person/' . mb_strtolower($this->appSetup->info->project) . '"},
            "headline":"' . $seo_info->title . '",
            "mainEntityOfPage":{"@id":"' . $this->Router->getURL(true) . '#' . $webpageid . '"},
            "publisher":{"@id":"' . $main_URL . '#organization"},
            "image":{"@id":"' . $this->Router->getURL(true) . '#' . md5($seo_info->image) . '"},
            "keywords":[' . implode(',', $article_keywords) . '],
            "articleSection":["' . $seo_info->title . '"],
            "inLanguage":"en-US"
        }';

        $s_footer = ']}';

        //addinfo
        $s_extra_info = [];
        if (is_array($addinfo) && count($addinfo) >= 1) {
            foreach ($addinfo as $info)
                array_push($s_extra_info, $info);
        }

        $plain_extra = (is_array($s_extra_info) && count($s_extra_info) >= 1) ? implode(',', $s_extra_info) : '';
        array_push($mainSchema, $s_def_organization, $s_def_website, $s_def_image, $s_def_webpage, $s_def_author);

        //berads
        if (!empty($s_def_breads))
            array_push($mainSchema, $s_def_breads);

        //article
        if (!empty($s_def_article))
            array_push($mainSchema, $s_def_article);
        //extras
        if (!empty($plain_extra))
            array_push($mainSchema, $plain_extra);

        $s_midinfo = implode(',', $mainSchema);

        //output	
        $tominify = $s_head . $s_midinfo . $s_footer;
        $minify = preg_replace('/[\t]+/', '', preg_replace('/[\r\n]+/', "\n", str_replace(PHP_EOL, '', $tominify)));

        if ($print) {
            echo PHP_EOL . '<script type="application/ld+json" class="fnx-schema-graph">' . $minify . '</script>';
        } else {
            return '<script type="application/ld+json" class="fnx-schema-graph">' . $minify . '</script>';
        }
    }

    /**
     * getSEOInfo
     * 
     * @param string $filepath
     */
    public function getSEOInfo(string $filepath = '')
    {
        $seoinfo = (array) $this->appSetup->seo;
        if (file_exists($filepath)) {
            foreach ($seoinfo as $k => $v) {
                $pattern = "/#@" . $k . ":(.*)?/";
                preg_match_all($pattern, file_get_contents($filepath), $m);
                if (
                    $m
                    && isset($m[1])
                    && !empty($m[1][0])
                ) {
                    $seoinfo[$k] = trim($m[1][0]);
                }
            }
        }
        return (object) $seoinfo;
    }
}