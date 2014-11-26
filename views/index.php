<!DOCTYPE html>
<html>
<head>
<title>Dive into the Red Water</title>
<link type="text/css" rel="stylesheet" href="style.css" media="screen"/>
</head>
<body>
<center>
<h1>Dive into the Red Water</h1>
<p>The web site that Gareth wanted</p>
<div>
<ul>
<?php foreach ($items as $item) { ?>
<li><figure>
  <a target="_blank" href="<? echo $item['url']?>">
    <img src="<? echo $item['src']?>" alt="<? echo $item['title'] ?>">
  </a>
  <figcaption>
    <a target="_blank" href="<? echo $item['url'] ?>"><? echo $item['title'] ?></a>
  </figcaption>
</figure></li>
<?php } ?>
</ul>
</div>
</center>
</body>
</html>
