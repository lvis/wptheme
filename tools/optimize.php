<?php
$pathToCurrentDir = dirname(__FILE__).DIRECTORY_SEPARATOR;
Placeholder::optimizeJPG("{$pathToCurrentDir}wmarked.jpg");
//echo exec('whoami');
//jpegtran -copy none -optimize -outfile min.image.jpg image.jpg