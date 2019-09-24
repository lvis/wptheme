<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com>*/
final class Placeholder {
    const BASE_DIR = 'basedir';
    const BASE_URL = 'baseurl';
    const POSITION_BOTTOM_RIGHT = "positionBottomRight";
    const POSITION_BOTTOM_LEFT = "positionBottomLeft";
    const POSITION_TOP_RIGHT = "positionTopRight";
    const POSITION_TOP_LEFT = "positionTopLeft";
    const POSITION_CENTER = "positionCenter";
    private static $uploadDirPath = "";
    private static $uploadDirUrl = "";

    static function getUploadDirUrl() {
        if (!self::$uploadDirUrl) {
            self::$uploadDirUrl = self::getUploadPath(self::BASE_URL) . DIRECTORY_SEPARATOR;
        }
        return self::$uploadDirUrl;
    }

    static function getUploadPath($pathType = self::BASE_DIR) {
        $path = "";
        $uploadDirs = wp_get_upload_dir();
        if (is_array($uploadDirs) && isset($uploadDirs[$pathType])) {
            $path = $uploadDirs[$pathType];
        }
        return $path;
    }

    static function getImageAbsoluteUrl($imageId, $size = WPImages::FULL) {
        $absoluteUrl = "";
        $image = image_downsize($imageId, $size);
        if (is_array($image) && isset($image[0])) {
            $absoluteUrl = $image[0];
        }
        return $absoluteUrl;
    }

    static function optimizeJPG($filePath) {
        $result = "";
        $pathToJpegTran = WP_CONTENT_DIR . "/ewww/jpegtran";
        if (is_executable($pathToJpegTran) && file_exists($filePath)) {
            exec("$pathToJpegTran -copy none -optimize -outfile $filePath $filePath", $output, $result);
            //echo var_dump($output)."<br>".$rOutput;
        }
        return $result;
    }

    static function getPlaceHolder($size = WPImages::THUMB, $attr = null) {
        list($imgWidth, $imgHeight) = self::getImgSize($size);
        $logoId = get_theme_mod(WPOptions::SITE_LOGO);
        if (!$logoId) {
            $logoId = get_bloginfo('name');
        }
        $imgScaledName = sprintf("%splaceholder%sx%s.png", $logoId, $imgWidth, $imgHeight);
        $imgScaledPath = self::getUploadDirPath() . $imgScaledName;
        $imgScaledUrl = self::getUploadPath(self::BASE_URL) . DIRECTORY_SEPARATOR . $imgScaledName;
        if (file_exists($imgScaledPath)) {
            $result = $imgScaledUrl;
        }
        else {
            $image = self::createImageResourceFromColor($imgWidth, $imgHeight);
            $imgResult = false;
            if (is_numeric($logoId)) {
                $pathToLogo = get_attached_file($logoId); //Path
                $imgWatermark = self::imageCreateFromPath($pathToLogo);
                $imgResult = self::renderImage($image, $imgWatermark, Placeholder::POSITION_CENTER, 100);
                imagedestroy($imgWatermark);
            }
            if (!$imgResult) {
                $imgResult = self::renderText($image, get_bloginfo('name'));
            }
            $imgResultScaled = imagecreatetruecolor($imgWidth, $imgHeight);
            imagecopyresized($imgResultScaled, $imgResult, 0, 0, 0, 0, $imgWidth, $imgHeight, imagesx($imgResult), imagesy($imgResult));
            imagedestroy($imgResult);
            //TODO Find best way to Optimize scaled image  and Extract Quality Parameter to site config
            imagejpeg($imgResultScaled, $imgScaledPath, 50);
            imagedestroy($imgResultScaled);
            $result = $imgScaledUrl;
        }
        if (is_array($attr)) {
            $attr = self::getImageAttr($attr);
            $result = "<img src='$result' width='$imgWidth' height='$imgHeight' $attr/>";
        }
        return $result;
    }

    /**
     * @param $size
     *
     * @return array
     */
    public static function getImgSize($size) {
        $imgWidth = 0;
        $imgHeight = 0;
        if (in_array($size, [WPImages::THUMB, WPImages::MEDIUM, WPImages::LARGE, WPImages::FULL])) {
            $imgWidth = get_option($size . '_size_w');
            $imgHeight = get_option($size . '_size_h');
            return [$imgWidth, $imgHeight];
        }
        else if (is_array($size) && count($size) == 2) {
            $imgWidth = $size[0];
            $imgHeight = $size[1];
            return [$imgWidth, $imgHeight];
        }
        else if (is_int($size)) {
            $imgWidth = $size;
            $imgHeight = $size;
            return [$imgWidth, $imgHeight];
        }
        return [$imgWidth, $imgHeight];
    }

    static function getUploadDirPath() {
        if (!self::$uploadDirPath) {
            self::$uploadDirPath = self::getUploadPath() . DIRECTORY_SEPARATOR;
        }
        return self::$uploadDirPath;
    }

    static function createImageResourceFromColor($width, $height) {
        if (!is_numeric($width) || $width <= 0) {
            $width = get_option(WPImages::THUMB . '_size_w');
        }
        if (!is_numeric($height) || $height <= 0) {
            $height = get_option(WPImages::THUMB . '_size_h');
        }
        $image = imagecreatetruecolor($width, $height);
        $backgroundColorIndex = imagecolorallocate($image, 230, 230, 230);
        imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColorIndex);
        return $image;
    }

    static function imageCreateFromPath($filename) {
        $imageResource = false;
        if (is_file($filename)) {
            $size = getimagesize($filename);
            if (is_array($size) && count($size) > 2) {
                switch ($size[2]) {
                case IMAGETYPE_JPEG:
                case IMAGETYPE_JPEG2000:
                    {
                        $imageResource = imagecreatefromjpeg($filename);
                        break;
                    }
                case IMAGETYPE_PNG:
                    {
                        $imageResource = imagecreatefrompng($filename);
                        break;
                    }
                }
            }
        }
        return $imageResource;
    }

    private static function renderImage($image, $watermark, $position, $alpha = 50) {
        // Set the margins for the stamp and get the height/width of the stamp image
        $marginRight = 10;
        $marginBottom = 10;
        $imageWith = imagesx($image);
        $imageHeight = imagesy($image);
        $imageWaterMarkWith = imagesx($watermark);
        $imageWaterMarkHeight = imagesy($watermark);
        // Copy the stamp image onto our photo using the margin offsets and the photo
        // width to calculate positioning of the stamp.
        switch ($position) {
        case self::POSITION_CENTER:
            {
                $imageWaterMarkPosX = ($imageWith - $imageWaterMarkWith) / 2;
                $imageWaterMarkPosY = ($imageHeight - $imageWaterMarkHeight) / 2;
                break;
            }
        case self::POSITION_BOTTOM_RIGHT:
        default:
            {
                $imageWaterMarkPosX = $imageWith - $imageWaterMarkWith - $marginRight;
                $imageWaterMarkPosY = $imageHeight - $imageWaterMarkHeight - $marginBottom;
                break;
            }
        }
        return self::imageCopyMergeAlpha($image, $watermark, $imageWaterMarkPosX, $imageWaterMarkPosY, 0, 0, $imageWaterMarkWith, $imageWaterMarkHeight, $alpha);
        //imagecopy( $image, $imageWaterMark, $imageWaterMarkPosX, $imageWaterMarkPosY, 0, 0, $imageWaterMarkWith, $imageWaterMarkHeight);
    }

    static function imageCopyMergeAlpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
        return $dst_im;
    }

    static function renderText($image, $text = "") {
        if (empty($text) || is_string($text) == false) {
            $text = "";
        }
        /*
         * http://php.net/manual/en/function.imagettftext.php
         * http://stackoverflow.com/questions/27682284/php-imagettftext-center-text
         * https://forums.phpfreaks.com/topic/99393-gd-and-imagettftext-center-variable-length-string/
         * */
        //TODO Trasnform Hexcolor from param to RGB
        $textColorIndex = imagecolorallocate($image, 0, 0, 0);
        $fontPath = __DIR__ . '/libs/Lato-Regular.ttf';
        //TODO Make Possible to adjust fonts according to Theme and refactor code for case when no font available
        $box = imagettfbbox(20, 0, $fontPath, $text);
        $x = (imagesx($image) / 2) - (($box[2] - $box[0]) / 2);
        $y = (imagesy($image) / 2) - (($box[3] - $box[1]) / 2);
        imagettftext($image, 20, 0, $x, $y, $textColorIndex, $fontPath, $text);
        return $image;
    }

    static function getImageAttr($attr) {
        $result = "";
        if (is_array($attr)) {
            foreach ($attr as $key => $value) {
                if ($value) {
                    $result .= " $key='$value'";
                }
            }
        }
        else {
            if ($attr) {
                $result = $attr;
            }
        }
        return $result;
    }

    static function getScaledImage($imgId, $size = WPImages::THUMB, $attr = null) {
        list($imgWidth, $imgHeight) = self::getImgSize($size);
        $imgPath = get_attached_file($imgId);
        if (is_file($imgPath)) {
            $imgUrl = wp_get_attachment_url($imgId);
        }
        $result = "";
        if (is_file($imgPath)) {
            $imgName = pathinfo($imgPath, PATHINFO_FILENAME);
            //$imgWMarkedName = "$imgName-scaled";
            $imgScaledName = "$imgId-{$imgWidth}x{$imgHeight}-scaled";
            $imgScaledPath = self::str_replace_last($imgName, $imgScaledName, $imgPath);
            $imgScaledUrl = self::str_replace_last($imgName, $imgScaledName, $imgUrl);
            if (file_exists($imgScaledPath) == false) {
                $imgResult = self::imageCreateFromPath($imgPath);
                //Add resize image code
                $imgResultScaled = imagecreatetruecolor($imgWidth, $imgHeight);
                imagecopyresized($imgResultScaled, $imgResult, 0, 0, 0, 0, $imgWidth, $imgHeight, imagesx($imgResult), imagesy($imgResult));
                imagedestroy($imgResult);
                //TODO Find best way to Optimize scaled image  and Extract Quality Parameter to site config
                imagejpeg($imgResultScaled, $imgScaledPath, 50);
                //                    self::optimizeJPG($imgWMarkedPath);
                imagedestroy($imgResultScaled);
                $result = $imgScaledUrl;
            }
            else {
                $result = $imgScaledUrl;
            }
            if (is_array($attr)) {
                $attr = self::getImageAttr($attr);
                $result = "<img src='$result' width='$imgWidth' height='$imgHeight' $attr/>";
            }
        }
        return $result;
    }

    static function str_replace_last($search, $replace, $subject) {
        $pos = strrpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }
}