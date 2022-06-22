<?php

namespace app\traits;

use app\controllers\classes\Functions;
use DOMDocument;

trait SiteMap{


    public static function generation($source){

        $dom = new DOMDocument('1.0', 'UTF-8');

        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $buildCatLinks = (CONFIG_SYSTEM["seo_type"] == '3' || CONFIG_SYSTEM["seo_type"] == '4') ? Functions::buildCatLinks($source["categories"]) : '';

        $ids = [];
        foreach ($source["posts"] as $row) {

            if(in_array($row["id"], $ids)) continue;
            $ids[] = $row["id"];

            $url = $dom->createElement('url');
            $loc = $dom->createElement('loc');

            // FULL LINK
            $link = $row["url"].CONFIG_SYSTEM["seo_type_end"];
            if(CONFIG_SYSTEM["seo_type"] == '2')
                $link = $row["id"] . '-' . $link;
            if(CONFIG_SYSTEM["seo_type"] == '3')
                $link = $buildCatLinks[$row["category_id"]]["urls"].'/' . $link;
            if(CONFIG_SYSTEM["seo_type"] == '4')
                $link = $buildCatLinks[$row["category_id"]]["urls"].'/' . $row["id"] . '-' . $link;
            $link = 'http'.(!empty(CONFIG_SYSTEM["ssl"])?'s':'').'://'.CONFIG_SYSTEM["home"].'/'.$link;

            $text = $dom->createTextNode(htmlentities($link, ENT_QUOTES));
            $loc->appendChild($text);
            $url->appendChild($loc);

            $lastmod = $dom->createElement('lastmod');
            $text = $dom->createTextNode(date('Y-m-d', $row["last_modify"]));
            $lastmod->appendChild($text);
            $url->appendChild($lastmod);

            // Элемент <priority> - приоритетность (от 0 до 1.0, по умолчанию 0.5).
            // Если дата публикации/изменения статьи была меньше недели назад ставим приоритет 1.
            $priority = $dom->createElement('priority');
            $text = $dom->createTextNode((($row["last_modify"] + 604800) > time()) ? '1' : '0.5');
            $priority->appendChild($text);
            $url->appendChild($priority);

            $urlset->appendChild($url);
        }

        $dom->appendChild($urlset);

        $dom->save(ROOT . '/uploads/system/sitemap.xml');
    }


    public static function get(){

        if(file_exists(ROOT . '/uploads/system/sitemap.xml')){

            return file_get_contents(ROOT . '/uploads/system/sitemap.xml');

        } else{

            return false;
        }

    }

}