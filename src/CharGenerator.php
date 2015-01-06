<?php
namespace Mcustiel\Captcha;

class CharGenerator
{
    private $minRotation;
    private $maxRotation;
    private $minSize;
    private $maxSize;

    // In constructor
    private $fontsPath;
    private $fontsList;

    // In generate
    private $image;

    private $transparentColor;
    private $charColor;

    public function __construct($minSize, $maxSize, $minRotation, $maxRotation) {
        $this->fontsPath = getenv('GDFONTPATH');
        $this->fontsList = array_slice(scandir($this->fontsPath), 2);
        if (count($this->fontsList) == 0) {
            // throw exception
        }
        $this->minRotation = $minRotation;
        $this->maxRotation = $maxRotation;
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
    }

    public function generate($char, $width, $height)
    {
        $this->initImage($width, $height);
        $this->setRandomAntialias();
        $this->drawCharacter($char, $width, $height);
        $this->setRandomFilter();

        return $this->image;
    }

    private function setRandomFilter()
    {
        // Random filter
        if (rand(0, 1)) {
            imagefilter(
                $this->image, rand(0, 1) ? IMG_FILTER_GAUSSIAN_BLUR : IMG_FILTER_MEAN_REMOVAL
            );
        }
    }

    private function initImage($width, $height)
    {
        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }
        $this->image = imagecreatetruecolor($width, $height);
        $this->transparentColor = imagecolortransparent($this->image,
            imagecolorallocate($this->image, 255, 255, 255));
        imagefill($this->image, 0, 0, $this->transparentColor);
        $this->charColor = imagecolorallocate(
            $this->image,
            rand(0, 0x64),
            rand(0, 0x64),
            rand(0, 0x64)
        );
    }

    private function drawCharacter($char, $width, $height)
    {
        // Rotation with angle and durection
        $rotationAngle = abs(rand($this->minRotation, $this->maxRotation) - (rand(0, 1) ? 360 : 0));
        $size = rand($this->minSize, $this->maxSize);

        imagettftext(
            $this->image,
            $size,
            $rotationAngle,
            rand(floor($width / 8), floor($width / 4)),
            rand(floor($height * 3 / 4), floor($height * 6 / 7)),
            $this->charColor,
            $this->fontsList[rand(0, count($this->fontsList) - 1)],
            $char
        );
    }

    private function setRandomAntialias()
    {
        if (function_exists('imageantialias')) {
            imageantialias($this->image, rand(0, 1));
        }
    }
}
