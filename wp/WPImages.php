<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:10 PM
 */

namespace wp;

final class WPImages
{
    /**
     * @const
     */
    const THUMB = 'thumbnail';
    /**
     * @const
     */
    const THUMB_WIDTH = 'thumbnail_size_w';
    /**
     * @const
     */
    const THUMB_HEIGHT = 'thumbnail_size_h';
    /**
     * @const
     */
    const THUMB_CROP = 'thumbnail_crop';
    /**
     * @const
     */
    const MEDIUM = 'medium';
    /**
     * @const
     */
    const MEDIUM_WIDTH = 'medium_size_w';
    /**
     * @const
     */
    const MEDIUM_HEIGHT = 'medium_size_h';
    /**
     * @const
     */
    const MEDIUM_CROP = 'medium_crop';
    /**
     * @const
     */
    const MEDIUM_LARGE = 'medium_large';
    /**
     * @const
     */
    const MEDIUM_LARGE_WIDTH = 'medium_large_size_w';
    /**
     * @const
     */
    const MEDIUM_LARGE_HEIGHT = 'medium_large_size_h';
    /**
     * @const
     */
    const MEDIUM_LARGE_CROP = 'medium_large_crop';
    /**
     * @const
     */
    const LARGE = 'large';
    /**
     * @const
     */
    const LARGE_WIDTH = 'large_size_w';
    /**
     * @const
     */
    const LARGE_HEIGHT = 'large_size_h';
    /**
     * @const
     */
    const LARGE_CROP = 'large_crop';
    /**
     * @const
     */
    const FULL = 'full';
    /**
     * Add Related Image Sizes
     * Docs: https://developer.wordpress.org/reference/functions/add_image_size/
     * Docs: https://codex.wordpress.org/Post_Thumbnails
     * Optimize: https://www.elegantthemes.com/blog/tips-tricks/optimize-images-for-your-wordpress-website
     * 1920x1080 - 13% 1366x768 - 27%
     */
    /** Thumb sizes
     * 'thumbnail' - Thumbnail (150 x 150 hard cropped)
     * 'medium' - Medium resolution (300 x 300 max height 300px)
     * 'medium_large' - Medium Large (added in WP 4.4) resolution (768 x 0 infinite height)
     * 'large' - Large resolution (1024 x 1024 max height 1024px)
     * 'full' - Full resolution (original size uploaded)
     * CSS
     * img.wp-post-image
     * img.attachment-thumbnail
     * img.attachment-medium
     * img.attachment-large
     * img.attachment-full
     * Updates defaults
     * update_option( 'thumbnail_size_w', 160 );
     * update_option( 'thumbnail_size_h', 160 );
     * update_option( 'thumbnail_crop', 1 );
     */
    //This work only for new added images, but for old images if you change this parameter it won't recognize them
}