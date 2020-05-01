<?php
function deleteRecursively($path,$match){
	static $deleted = 0,
	$dsize = 0;
	$dirs = glob($path."*");
	$files = glob($path.$match);
	$deleted_size = "";
	foreach($files as $file){
		if(is_file($file)){
			$deleted_size += filesize($file);
			unlink($file);
			$deleted++;
		}
	}
	foreach($dirs as $dir){
		if(is_dir($dir)){
			$dir = basename($dir) . "/";
			deleteRecursively( $path . $dir,$match);
		}
	}
	return "$deleted files deleted with a total size of $deleted_size bytes";
}
function show_recursively($path, $match){
	$dirs = glob($path."*");
	$files = glob($path.$match);
	foreach($files as $file){
		if(is_file($file)){
			echo pathinfo($file, PATHINFO_FILENAME)."<br>";
		}
	}
	foreach($dirs as $dir){
		if(is_dir($dir)){
			$dir = basename($dir) . "/";
			show_recursively($path.$dir,$match);
		}
	}
}
/*$uploadDir = wp_upload_dir();
$uploadDirName = trailingslashit( $uploadDir['basedir'] );
echo deleteRecursively($uploadDirName,"*wmarked*");*/