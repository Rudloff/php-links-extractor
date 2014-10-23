<?php
/**
 * Recursively extract all the links from a website
 *
 * PHP version 5.4.4
 *
 * @category Utility
 * @package  LinksExtractor
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU General Public License http://www.gnu.org/licenses/gpl.html
 * @link     https://github.com/Rudloff/php-links-extractor
 * */
?>
<!DOCTYPE HTML>
<head>
    <meta charset="UTF-8" />
    <title>Links Extractor</title>
</head>
<body>
<ul>
<?php
require 'absoluteurl/url_to_absolute.php';
$rootURL = $_GET['website'];
$baseHost = parse_url($rootURL);
$pastURLs = array();
$links = array();

/**
 * Recursively get all the links from a webpage
 *
 * @param string $baseURL Base URL
 *
 * @return void
 * */
function getLinks($baseURL)
{
    global $links, $pastURLs, $baseHost;
    if (!in_array($baseURL, $pastURLs)) {
        $pastURLs[] = $baseURL;
        $doc = new DOMDocument();
        @$doc->loadHTMLFile($baseURL);
        $xpath = new DOMXPath($doc);
        $tags = $xpath->query('.//a');
        foreach ($tags as $tag) {
            $url = $tag->getAttribute('href');
            $urlinfo = parse_url($url);
            if (isset($urlinfo['host'])) {
                if ($urlinfo == $baseHost) {
                    getLinks($url);
                }
            } else if (!isset($urlinfo['scheme'])) {
                @$url = url_to_absolute($baseURL, $url);
                getLinks($url);
            }
            if (!in_array($url, $links)) {
                echo '<li><a href="'.$url.'">', $url, '</a></li>';
                $links[]=$url;
            }
            flush();
        }
    }
}

getLinks($rootURL);
?>
</ul>
</body>
