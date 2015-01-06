<?php
namespace Mcustiel\Captcha;

class CharGenerator
{
    // Created inside this class
    private $minRotation;
    private $maxRotation;

    // In constructor
    private $fontsPath;
    private $fontsList;

    // In generate
    private $image;

    private $transparentColor;
    private $charColor;

    public function __construct($minRotation, $maxRotation) {
        $this->fontsPath = getenv('GDFONTPATH');
        $this->fontsList = array_slice(scandir($this->fontsPath), 2);
        if (count($this->fontsList) == 0) {
            // throw exception
        }
        $this->minRotation = $minRotation;
        $this->maxRotation = $maxRotation;
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

        imagettftext(
            $this->image,
            $height,
            $rotationAngle,
            1,
            $height - 1,
            $this->charColor,
            $this->fontsList[rand(0, count($this->fontsList) - 1)],
            $char
        );
        //$this->rotate($rotationAngle);
    }

    private function rotate($rotationAngle)
    {
        $rotated = imagerotate($this->image, $rotationAngle, $this->transparentColor, 1);
        imagedestroy($this->image);
        $this->image = $rotated;
    }


    private function setRandomAntialias()
    {
        if (function_exists('imageantialias')) {
            imageantialias($this->image, rand(0, 1));
        }
    }
}
