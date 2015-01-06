<?php
namespace Mcustiel\Captcha;

class CaptchaGenerator
{
    private $black;
    private $white;
    private $colors;

    private $width = 200;
    private $height = 50;
    private $minRotation = 1;
    private $maxRotation = 15;
    private $charMinHeight = 24;
    private $charMaxHeight = 36;

    private $charsCount;
    private $code;
    private $image;

    public function __construct(
        $code, $width = 200, $height = 50, $minRotation = 1,
        $maxRotation = 15, $charMinHeight = 20, $charMaxHeight = 32,
        $fontsPath = null
    ) {
        $this->setFontsPathEnv($fontsPath);
        $this->width = $width;
        $this->height = $height;
        $this->charsCount = strlen($code);
        $this->code = $code;
        $this->minRotation = $minRotation;
        $this->maxRotation = $maxRotation;
        $this->charMinHeight = $charMinHeight;
        $this->charMaxHeight = $charMaxHeight;
    }

    private function setFontsPathEnv($fontsPath)
    {
        if (getenv('GDFONTPATH') === false) {
            if (defined('SIMPLECAPTCHA_FONTS_PATH')) {
                $path = realpath(SIMPLECAPTCHA_FONTS_PATH);
            } elseif (!empty($fontsPath)) {
                $path = $fontsPath;
            }
            if (is_dir($path)) {
                putenv('GDFONTPATH=' . $path);
            } else {
                // throw exception;
            }
        }
    }

    public function generate()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $this->initBasicColors();
        $this->setRandomColors();
        $this->setBackgroundNoise();
        $this->setRandomAntialias();

        $this->drawEllipses();
        $this->drawLines();
        $this->drawCharacters();

        return $this->image;
    }

    private function drawCharacters()
    {
        $charWidth = $this->initCharactersParamsAndGetCharWidth();

        $charGenerator = new CharGenerator($this->minRotation, $this->maxRotation);
        // Ingreso caracteres rotados de forma aleatoria
        for ($index = 0; $index < $this->charsCount; $index++) {
            $charHeight = rand($this->charMinHeight, $this->charMaxHeight);

            $charImage = $charGenerator->generate($this->code[$index], $charWidth, $charHeight);
            imagecopymerge(
                $this->image,
                $charImage,
                $index * $charWidth,
                rand(0, $this->height - $charHeight),
                0,
                0,
                $charWidth,
                imagesy($charImage),
                rand(70, 100));
            imagedestroy($charImage);
        }

    }

    private function initCharactersParamsAndGetCharWidth()
    {
        // Stablish line thickness for chars and width of characters
        // (with 2 pixel separation between them)
        imagesetthickness($this->image, 1);
        return floor(($this->width - $this->charsCount * 2) / $this->charsCount);
    }

    private function drawLines()
    {
        // Creo líneas de ancho, posición y dirección aleatorios
        for ($i = 0; $i < 2; $i ++) {
            imagesetthickness($this->image, rand(1, 2));
            imageline(
                $this->image,
                0,
                rand(0, $this->height),
                $this->width - 1, rand(0, $this->height),
                $this->black
            );
        }
    }

    private function drawEllipses()
    {
        // draw two ellipses with random size and position
        imageellipse(
            $this->image,
            0,
            $this->height * rand(0, 1),
            rand($this->width * 1 / 2, $this->width * 4 / 5),
            rand($this->height * 1 / 4, $this->height * 3 / 4),
            $this->black
        );
        imageellipse(
            $this->image, $this->width,
            $this->height * rand(0, 1),
            rand($this->width * 1 / 4, $this->width * 3 / 4),
            rand($this->height * 1 / 4, $this->height * 3 / 4),
            $this->black
        );

    }

    private function setRandomAntialias()
    {
        // Random Antialias
        if (function_exists('imageantialias') && rand(0, 1)) {
            imageantialias($this->image, true);
        }
    }

    private function setBackgroundNoise()
    {
        // Inserts random pixels with random colors
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                imagesetpixel(
                    $this->image,
                    $x,
                    $y,
                    rand(0, 1) ? $this->white : $this->colors[rand() % 3]
                );
            }
        }
    }

    private function initBasicColors()
    {
        $this->black = imagecolorallocate($this->image, 0, 0, 0);
        $this->white = imagecolorallocate($this->image, 0XFF, 0XFF, 0XFF);
    }

    private function setRandomColors()
    {
        $this->colors = [];
        // Three random colors
        $this->colors[0] = imagecolorallocate($this->image, rand(0x7F, 0XFF), rand(0x7F, 0XFF), rand(0x7F, 0XFF));
        $this->colors[1] = imagecolorallocate($this->image, rand(0x7F, 0XFF), rand(0x7F, 0XFF), rand(0x7F, 0XFF));
        $this->colors[2] = imagecolorallocate($this->image, rand(0x7F, 0XFF), rand(0x7F, 0XFF), rand(0x7F, 0XFF));
    }
}

