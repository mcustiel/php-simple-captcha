<?php
namespace Mcustiel\Captcha;

class CaptchaGenerator
{
    private $black;
    private $white;
    private $colors;

    private $width;
    private $height;
    private $minRotation;
    private $maxRotation;
    private $charMinSize;
    private $charMaxSize;

    private $image;

    public function __construct(GeneratorConfig $config)
    {
        $this->setFontsPathEnv($config->getFontsPath());
        $this->width = $config->getWidth();
        $this->height = $config->getHeight();
        $this->minRotation = $config->getMinCharacterRotation();
        $this->maxRotation = $config->getMaxCharacterRotation();
        $this->charMinSize = $config->getMinCharacterSize();
        $this->charMaxSize = $config->getMaxCharacterSize();
    }

    private function setFontsPathEnv($fontsPath)
    {
        $env = getenv('GDFONTPATH');
        if (empty($fontsPath)
            && $env === false
            && defined('SIMPLECAPTCHA_FONTS_PATH')
            && is_dir(SIMPLECAPTCHA_FONTS_PATH)) {
            putenv('GDFONTPATH=' . SIMPLECAPTCHA_FONTS_PATH);
        } elseif (is_dir($fontsPath)) {
            putenv('GDFONTPATH=' . $fontsPath);
        } else if ($env === false) {
            throw new NoFontsPathDefinedException();
        }
    }

    public function generate($code)
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $this->initBasicColors();
        $this->setRandomColors();
        $this->setBackgroundNoise();
        $this->setRandomAntialias();

        $this->drawEllipses();
        $this->drawLines();
        $this->drawCharacters($code);

        return $this->image;
    }

    private function drawCharacters($code)
    {
        $charsCount = strlen($code);
        $charWidth = $this->initCharactersParamsAndGetCharWidth($code);

        $charGenerator = new CharGenerator($this->charMinSize, $this->charMaxSize, $this->minRotation, $this->maxRotation);
        // Add randomly placed and rotated characters
        for ($index = 0; $index < $charsCount; $index++) {


            $charImage = $charGenerator->generate($code[$index], $charWidth, $this->height);
            imagecopymerge(
                $this->image,
                $charImage,
                $index * $charWidth,
                0,
                0,
                0,
                $charWidth,
                imagesy($charImage),
                rand(70, 100));
            imagedestroy($charImage);
        }
    }

    private function initCharactersParamsAndGetCharWidth($code)
    {
        // Stablish line thickness for chars and width of characters
        // (with 2 pixel separation between them)
        $charsCount = strlen($code);
        imagesetthickness($this->image, 1);
        return floor(($this->width - $charsCount * 2) / $charsCount);
    }

    private function drawLines()
    {
        // Add 2 lines with random thickness, position and direction.
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

