<?php
define(REDWATER, "this is red water");

$user = '110243769946590180531';
$album = '6085722761090100369';
$feedurl = "http://photos.googleapis.com/data/feed/base/user/$user/albumid/$album?kind=photo&alt=rss";
$albumurl = "http://plus.google.com/photos/$user/albums/$album";

function read_feed($feedurl) {
  if (($feed = fopen($feedurl, 'r')) === false) {
    return false;
  }
  $contents = '';
  $chunk_size = 8192;
  while (!feof($feed)) {
    $contents .= fread($feed, $chunk_size);
  }
  fclose($feed);
  $doc = simplexml_load_string($contents);
  return $doc;
}

function parse_feed_items($doc, $albumurl) {
  $items = array();
  $result = $doc->xpath('/rss/channel/item');
  foreach ($result as &$node) {
    $item = array();
    $item['title'] = (string)$node->title;
    $guid = $node->guid;
    if (preg_match('/photoid\/(\d+)/', $node->guid, $m) !== 1) {
      return false;
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
	return false;
      }
      error_log('image url is ' . $url);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);
      $img = imagecreatefromstring($data);
      imagefilter($img, IMG_FILTER_COLORIZE, 96, -32, -32);
      imagejpeg($img, $item['src']);
    }

    $items[] = $item;
  }
  return $items;
}

$doc = read_feed($feedurl);
if ($doc === false) {
  require_once('views/error.php');
  return;
}

$items = parse_feed_items($doc, $albumurl);
if ($items === false) {
  require_once('views/error.php');
  return;
}

require_once('views/index.php');
