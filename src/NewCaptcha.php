<?php
namespace Mcustiel\Captcha;

class Captcha
{
    const FONTS_COUNT = 3;
    const IMG_WIDTH = 200;
    const IMG_HEIGHT = 50;
    const CHARS_COUNT = 5;
    const CHAR_MIN_HEIGHT = 20;
    const CHAR_MAX_HEIGHT = 32;
    const MIN_ROTATION = 3;
    const MAX_ROTATION = 15;
    const MAX_SHIFT = 3;

    /**
     *
     * @var string
     */
    private $code = null;

    /**
     *
     * @var image_resource
     */
    private $image = null;

    private $characters = array(
        array(48, 57), // Digits
        array(65, 90) // Uppercase letters
    );

    public function code() {

    }

    public function getCode()
    {
        return $this->code;
    }

    public function getImageResource()
    {
        return $this->image;
    }

    private function generateCaptcha()
    {

    }

    public function destroy()
    {
        imagedestroy($this->image);
    }
}
