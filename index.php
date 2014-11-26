<?php
define(REDWATER, "this is red water");

$user = '110243769946590180531';
$album = '6085722761090100369';
$feedurl = "http://photos.googleapis.com/data/feed/base/user/$user/albumid/$album?kind=photo&alt=rss";
$albumurl = "http://plus.google.com/photos/$user/albums/$album";

if (($feed = fopen($feedurl, 'r')) === false) {
  require_once('views/error.php');
  return;
}
$contents = '';
$chunk_size = 8192;
while (!feof($feed)) {
  $contents .= fread($feed, $chunk_size);
}
fclose($feed);

$doc = simplexml_load_string($contents);

$items = array();
$result = $doc->xpath('/rss/channel/item');
foreach ($result as &$node) {
  $item = array();
  $item['title'] = (string)$node->title;
  $guid = $node->guid;
  if (preg_match('/photoid\/(\d+)/', $node->guid, $m) !== 1) {
    require_once('views/error.php');
    return;
  }
  $photoid = $m[1];
  $item['url'] = "$albumurl/$photoid";
  
  $enclosure = $node->enclosure;
  $pubDate = $node->pubDate;
  $type = $enclosure['type'];
  $url = $enclosure['url'];

  $filename = basename($url);

  $item['src'] = "images/$filename";
  if (!file_exists($item['src'])) {
    if ($type != 'image/jpeg') {
      require_once('view/error.php');
      return;
    }
    $img = imagecreatefromjpeg($url);
    imagefilter($img, IMG_FILTER_COLORIZE, 96, -32, -32);
    imagejpeg($img, $item['src']);
  }

  $items[] = $item;
}

require_once('views/index.php');

