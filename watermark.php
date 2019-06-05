<?php
/**
*
* @package Kleeja_up_helpers
* @copyright (c) 2007-2012 Kleeja.com
* @license ./docs/license.txt
*
*/

//no for directly open
if (! defined('IN_COMMON'))
{
    exit();
}

/**
 * This helper is used to make a watermark on a given image,
 * return nothing because if it work then ok , and if not then ok too :)
 * @todo text support
 *
 * @param $name
 * @param $ext
 * @return bool|void
 */
function adv_helper_watermark($name, $ext)
{
    global $config;

    //is this file really exsits ?
    if (! file_exists($name))
    {
        return;
    }

    $path_to_watermark = PATH . ltrim($config['watermark_image_path'], '/');

    if ($config['watermark_type'] == 'image')
    {
        $src_logo = false;

        if (file_exists($path_to_watermark))
        {
            $watermark_ext = (array) explode('.', $path_to_watermark);
            $watermark_ext = array_pop($watermark_ext);
            $watermark_ext = str_replace('jpg', 'jpeg', strtolower($watermark_ext));

            if (in_array($watermark_ext, ['gif', 'png', 'png', 'jpeg']))
            {
                $func     = "imagecreatefrom{$watermark_ext}";
                $src_logo = $func($path_to_watermark);
            }
        }

        //no watermark pic
        if (! $src_logo)
        {
            return;
        }
    }

    //if there is imagick lib, then we should use it
    if (extension_loaded('imagick') || class_exists('Imagick'))
    {
        adv_helper_watermark_imagick($name, $ext, $path_to_watermark);
        return;
    }

    //now, lets work and detect our image extension
    if (stripos($ext, 'jp') !== false)
    {
        $src_img = @imagecreatefromjpeg($name);
    }
    elseif (stripos($ext, 'png') !== false)
    {
        $src_img = @imagecreatefrompng($name);
    }
    elseif (stripos($ext, 'gif') !== false)
    {
        return;
    }
    elseif (stripos($ext, 'bmp') !== false)
    {
        if (! defined('BMP_CLASS_INCLUDED'))
        {
            include dirname(__file__) . '/BMP.php';
            define('BMP_CLASS_INCLUDED', true);
        }

        $src_img = imagecreatefrombmp($name);
    }
    else
    {
        return;
    }

    //detect width, height for the image
    $bwidth  = @imagesx($src_img);
    $bheight = @imagesy($src_img);

    if ($config['watermark_type'] == 'image')
    {
        //get width, height for the watermark image
        $lwidth  = @imagesx($src_logo);
        $lheight = @imagesy($src_logo);

        if ($bwidth > $lwidth+15 &&  $bheight > $lheight+15)
        {
            //where exactly do we have to make the watermark ..
            [$src_x, $src_y] = adv_get_watermark_position($bwidth, $bheight, $lwidth, $lheight, 'image');

            //make it now, watermark it
            @imagealphablending($src_img, true);
            @imagecopy($src_img, $src_logo, $src_x, $src_y, 0, 0, $lwidth, $lheight);
        }
        else
        {
            return;
        }
    }
    else
    {
        //color
        $color      = adv_convert_color($config['watermark_text_color']);
        $font_color = imagecolorallocate($src_img, $color[0], $color[1], $color[2]);
        //background
        $background = adv_convert_color($config['watermark_text_background']);
        $font_bkg   = imagecolorallocate($src_img, $background[0], $background[1], $background[2]);

        $text_size = (int) $config['watermark_text_size'] == 0 ? 15 : $config['watermark_text_size'];

        if (
            empty($config['watermark_text_font']) ||
            $config['watermark_text_font'] == 'default' ||
            ! in_array($config['watermark_text_font'], ['amiri', 'flat', 'kacstoffice'])
            ) {
            $font_path = PATH . 'includes/arial.ttf';
        }
        else
        {
            $font_path = __DIR__ . '/' . $config['watermark_text_font'] . '.ttf';
        }

        $ttfsize = imagettfbbox($text_size, 0, $font_path, $config['watermark_text_content']);

        $lwidth  = abs($ttfsize[4] - $ttfsize[0]);
        $lheight = abs($ttfsize[5] - $ttfsize[1]);


        if ($bwidth > $lwidth+10 &&  $bheight > $lheight+15)
        {
            //where exactly do we have to make the watermark ..
            [$src_x, $src_y] = adv_get_watermark_position($bwidth, $bheight, $lwidth, $lheight);
        }
        else
        {
            return;
        }

        $text = trim($config['watermark_text_content']);

        //if contains arabic letters
        if (preg_match("/^[\p{Arabic}]+/u", $text))
        {
            include_once __DIR__ . '/Glyphs.php';
            $text  = (new I18N_Arabic_Glyphs)->utf8Glyphs($text);
        }

        //background
        if ($config['watermark_text_background_enable'])
        {
            // 0, 0 is the top left corner of the image.
            imagefilledrectangle($src_img, $src_x, $src_y, $src_x + $lwidth + 5, $src_y - $lheight - 10, $font_bkg);
        }

        //text
        imagettftext($src_img, $text_size, 0 /** angle */, $src_x + 2, $src_y - 6, $font_color, $font_path, $text);
    }


    if (strpos($ext, 'jp') !== false)
    {
        //no compression, same quality
        @imagejpeg($src_img, $name, 100);
    }
    elseif (strpos($ext, 'png') !== false)
    {
        //no compression, same quality
        @imagepng($src_img, $name, 0);
    }
    elseif (strpos($ext, 'gif') !== false)
    {
        @imagegif($src_img, $name);
    }
    elseif (strpos($ext, 'bmp') !== false)
    {
        @imagebmp($src_img, $name);
    }
}


//
// generate watermarked images by imagick
//
function adv_helper_watermark_imagick($name, $ext, $logo)
{
    global $config;


    $im = new Imagick($name);

    if ($config['watermark_type'] == 'image')
    {
        $watermark = new Imagick($logo);
        $wWidth    = $watermark->getImageWidth();
        $wHeight   = $watermark->getImageHeight();
        $iWidth    = $im->getImageWidth();
        $iHeight   = $im->getImageHeight();
        [$x, $y]   = adv_get_watermark_position($iWidth, $iHeight, $wWidth, $wHeight, 'image');
    }
    else
    {
        // Create a new drawing palette
        $draw = new ImagickDraw();

        if (
            empty($config['watermark_text_font']) ||
            $config['watermark_text_font'] == 'default' ||
            ! in_array($config['watermark_text_font'], ['amiri', 'flat', 'kacstoffice'])
            ) {
            $font_path = PATH . 'includes/arial.ttf';
        }
        else
        {
            $font_path = __DIR__ . '/' . $config['watermark_text_font'] . '.ttf';
        }

        $draw->setFont($font_path);
        $draw->setFontSize($config['watermark_text_size']);
        $draw->setFillColor(new ImagickPixel($config['watermark_text_color']));

        // Position text at the bottom-right of the image
        $positions = ['tl' => Imagick::GRAVITY_NORTHWEST, 'tc' => Imagick::GRAVITY_NORTH, 'tr' => Imagick::GRAVITY_NORTHEAST,
            'bl'           => Imagick::GRAVITY_SOUTHWEST, 'bc' => Imagick::GRAVITY_SOUTH, 'br' => Imagick::GRAVITY_SOUTHEAST];
        $draw->setGravity($positions[$config['watermark_position']]);


        if ($config['watermark_text_background_enable'])
        {
            $draw->setTextUnderColor(new ImagickPixel($config['watermark_text_background']));
        }

        $draw->setTextEncoding('UTF-8');

        //if contains arabic letters
        if (preg_match("/^[\p{Arabic}]+/u", $config['watermark_text_content']))
        {
            include_once __DIR__ . '/Glyphs.php';
            $config['watermark_text_content']  = (new I18N_Arabic_Glyphs)->utf8Glyphs($config['watermark_text_content']);
        }
    }


    //calculate the position


    //an exception for gif image
    //generating thumb with 10 frames only, big gif is a devil
    if ($ext == 'gif')
    {
        $i = 0;

        foreach ($im as $frame)
        {
            if ($config['watermark_type'] == 'image')
            {
                $frame->compositeImage($watermark, imagick::COMPOSITE_OVER, $x, $y);
            }
            else
            {
                $frame->annotateImage($draw, 0, 0, 0, trim($config['watermark_text_content']));
            }

            if ($i >= 10)
            {
                // more than 10 frames, quit it
                break;
            }
            $i++;
        }
        $im->writeImages($name, true);
        return;
    }


    if ($config['watermark_type'] == 'image')
    {
        $im->compositeImage($watermark, imagick::COMPOSITE_OVER, $x, $y);
    }
    else
    {
        $im->annotateImage($draw, 10, 10, 0, ' ' . trim($config['watermark_text_content']) . ' ');
    }

    $im->writeImages($name, false);
}


function adv_convert_color($hex)
{
    $hex = ltrim($hex, '#');

    if (strlen($hex) < 6)
    {
        $hex = $hex[1] . $hex[1] . $hex[2] . $hex[2] . $hex[3] . $hex[3];
    }

    $a   = hexdec(substr($hex, 0, 2));
    $b   = hexdec(substr($hex, 2, 2));
    $c   = hexdec(substr($hex, 4, 2));

    return [$a, $b, $c];
}


function adv_get_watermark_position($bwidth, $bheight, $lwidth, $lheight, $type = 'text')
{
    global $config;

    if ($config['watermark_position'] == 'br' || empty($config['watermark_position']))
    {
        //bottom right
        $src_x = $bwidth  - ($lwidth + 10);
        $src_y = $bheight - ($lheight + 5);
    }
    elseif ($config['watermark_position'] == 'bl')
    {
        //bottom left
        $src_x = 10;
        $src_y = $bheight - ($lheight + 5);
    }
    elseif ($config['watermark_position'] == 'bc')
    {
        //bottom center
        $src_x = ($bwidth / 2) - ($lwidth / 2);
        $src_y = $bheight      - ($lheight + 5);
    }
    elseif ($config['watermark_position'] == 'tr')
    {
        //top right
        $src_x = $bwidth  - ($lwidth + 10);
        $src_y = ($type == 'image' ? 0 : $lheight) + 15;
    }
    elseif ($config['watermark_position'] == 'tl')
    {
        //top left
        $src_x = 10;
        $src_y = ($type == 'image' ? 0 : $lheight) + 15;
    }
    elseif ($config['watermark_position'] == 'tc')
    {
        //top center
        $src_x = ($bwidth / 2) - ($lwidth / 2);
        $src_y = ($type == 'image' ? 0 : $lheight) + 15;
    }

    return [$src_x, $src_y];
}
