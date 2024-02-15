<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Helpers;

/**
 * Class Crawler
 */
class Crawler
{
    /**
     * Default seenURLs variable
     */
    public $seenURLs = [];

    /**
     * Default crawledURLs variable
     */
    public $crawledURLs = [];

    /**
     * Default cachedXML variable
     */
    public $cachedXML;

    /**
     * Default setup variable
     */
    public $setup;

    /**
     * Crawler constructor
     */
    public function __construct($setup = []){
        $this->setup = $setup;
        $this->cachedXML = (
            $this->setup->sitemap->active == true
            && !empty($this->setup->sitemap->target)
            && $this->setup->sitemap->cache_file != ''
        ) ? $this->setup->sitemap->cache_file : '';
    }

    /**
     * crawlPage
     * 
     * @param string $url
     * @param int $depth
     * @param string $root
     * @param bool $keepdowmain
     */
    public function crawlPage($url, $depth = 5, $root='/', $keepdomain = true){
        #default
        $defurl = rtrim($url,'/');
        $this->crawledURLs[$defurl] = $defurl;

        $url = (mb_substr($url, -1) == '/') ? mb_substr($url, 0, -1) : $url;

        $_crawled = [];
        if( ($depth === 0) || (in_array($url, $this->seenURLs))){ return; }
        $this->seenURLs[] = $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec ($ch);
        curl_close ($ch);

        if( $result ){
            //$stripped_file = strip_tags($result, "<a>");
            $stripped_file = $result;

            /*
            preg_match_all("/<a[\s]+[^>]*?href[\s]?=[\s\"\']+"."(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/", $stripped_file, $matches, PREG_SET_ORDER ); 
            */
            preg_match_all("/<a[\s]+[^>]*?href[\s]?=[\s\"\']+(.*?)[\"\']+.*?>/", $stripped_file, $matches, PREG_SET_ORDER ); 

            foreach($matches as $match) {
                $href = $match[1];
                //var_dump($href);
                if ( mb_strpos($href, 'http') !== false ) {
                    $parts = parse_url($href);
                    //var_dump($parts);
                    $href = $parts['scheme'] . '://';
                    if ( isset($parts['user']) && isset($parts['pass']) ) { $href .= $parts['user'] . ':' . $parts['pass'] . '@'; }
                    $href .= $parts['host'];
                    if (isset($parts['port'])) { $href .= ':' . $parts['port']; }
                    if ( isset($parts['path']) ) { $href .= rtrim($parts['path'],'/'); }

                } else {
                    if ( $href[0] == '/' && mb_strlen($href) > 2) {
                        $href = trim($href,'/');
                        $href = $root . $href;
                    }
                }
                           
                if ($keepdomain == true) {
                    if ( mb_strpos($href, $root) !== false ) {
                        $this->crawlPage($href, $depth - 1,$root);
                    }
                } else {
                    $this->crawlPage($href, $depth - 1,$root);    
                }

                if ( mb_strpos($href, 'http') !== false ) {
                    if ( $keepdomain == false || mb_strpos($href, $root) !== false ) {
                        $this->crawledURLs[$href] = $href;
                    }
                }
            }
        }

        return true;
    }

    /**
     * getCrawled
     * 
     * @param string $main_url
     * @param int $crawler_depth
     */
    public function getCrawled($main_url,$crawler_depth) {
        $this->crawlPage($main_url,$crawler_depth,$main_url);
        return $this->crawledURLs;
    }

    /**
     * bakeSitemap
     * 
     * @param array $links
     */
    public function bakeSitemap() {

        $_sitemap = '';

        //is cached?
        if (
            file_exists($this->cachedXML)
            && ( date('U')-filemtime($this->cachedXML) ) < 86400
            && file_get_contents($this->cachedXML) != ''
        ) {
            //read cache
            $_sitemap = file_get_contents($this->cachedXML);
        } else {
            $source_target = (mb_substr($this->setup->sitemap->target, -1) === '/') ? $this->setup->sitemap->target : $this->setup->sitemap->target.'/';
            //new to cache
            $resource = $this->getCrawled($source_target,4);

            //bake file
            $_smtop     =   '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
                            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
            $_sm        = '';
            $_smbottom  = '</urlset>' . PHP_EOL;

            if ($resource && count($resource) >= 1 ) {
                sort($resource);
                foreach ($resource as $link) {
                    $_sm .= '<url>' . PHP_EOL .
                            '<loc>' . $link . '</loc>' . PHP_EOL .
                            '<changefreq>daily</changefreq>' . PHP_EOL .
                            '<priority>0.5</priority>' . PHP_EOL .
                            '</url>' . PHP_EOL;
                }
                $_sitemap = $_smtop . $_sm . $_smbottom;
                @file_put_contents($this->cachedXML, $_sitemap, LOCK_EX);
            }
        }

        header("Content-type: text/xml; charset=utf-8");
        echo $_sitemap;
    }
}