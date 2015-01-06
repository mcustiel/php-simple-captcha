<?php
namespace BackendTask\Image\Captcha;

define('FONTS_REAL_PATH', BASE_PROJECT_DIR . '/resources/fonts');

/**
 * THIS IS A REALLY OLD CLASS (I DEVELOPED IT IN 2008) THAT JUST WORKS.
 * I ADDED THE NAMESPACE SUPPORT, FORMATTED IT INTO PSR AND USED IT BECAUSE
 * I CAN'T USE ANY THIRD PARTY LIBRARIES FOR THIS PROJECT. (July 27, 2014)
 *
 * Clase Captcha. Trata de ser lo más aleatorio posible.
 *
 * @author Mariano Custiel
 *
 */
class Captcha
{
    const FONTS_PATH = FONTS_REAL_PATH;
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
        array(
            48,
            57
        ), // Dígitos
        array(
            65,
            90
        ) // Mayúsculas sin la Ñ
                   // array(97, 122), // Minúsculas sin la Ñ
    );

    function generateCode($_regenerate = false)
    {
        if ($this->code === null || $_regenerate) {
            $this->code = '';
            for ($i = 0; $i < self::CHARS_COUNT; $i ++) {
                $index = rand(0, count($this->characters) - 1);
                $this->code .= chr(rand($this->characters[$index][0], $this->characters[$index][1]));
            }
        }
        return $this->code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getCaptchaImage()
    {
        if ($this->image === null) {
            putenv('GDFONTPATH=' . realpath(self::FONTS_PATH));
            if (! $this->code) {
                $this->generateCode();
            }
            $this->generateImage();
        }
        return $this->image;
    }

    private function generateImage()
    {
        $this->image = imagecreatetruecolor(self::IMG_WIDTH, self::IMG_HEIGHT);

        // Tres colores aleatorios
        $color[0] = imagecolorallocate($this->image, rand(0x7F, 0XFF), rand(0x7F, 0XFF), rand(0x7F, 0XFF));
        $color[1] = imagecolorallocate($this->image, rand(0x7F, 0XFF), rand(0x7F, 0XFF), rand(0x7F, 0XFF));
        $color[2] = imagecolorallocate($this->image, rand(0x7F, 0XFF), rand(0x7F, 0XFF), rand(0x7F, 0XFF));

        $black = imagecolorallocate($this->image, 0, 0, 0); // Color negro
        $white = imagecolorallocate($this->image, 0XFF, 0XFF, 0XFF); // Color blanco

        // Inserto píxeles aleatorios con colores aleatorios.
        for ($y = 0; $y < 50; $y ++) {
            for ($x = 0; $x < 200; $x ++) {
                imagesetpixel($this->image, $x, $y, ($y % 2 ? ($x % 2 ? $color[rand() % 3] : $white) : ($x % 2 ? $white : $color[rand() % 3])));
            }
        }

        // Antialias aleatorio
        if (function_exists('imageantialias') && rand(0, 1)) {
            imageantialias($this->image, true);
        }

        // Creo dos elipses de tamaño y posición aleatorios
        imageellipse($this->image, 0, (self::IMG_HEIGHT * rand(0, 1)), rand(self::IMG_WIDTH * 1 / 2, self::IMG_WIDTH * 4 / 5), rand(self::IMG_HEIGHT * 1 / 4, self::IMG_HEIGHT * 3 / 4), $black);
        imageellipse($this->image, self::IMG_WIDTH, (self::IMG_HEIGHT * rand(0, 1)), rand(self::IMG_WIDTH * 1 / 4, self::IMG_WIDTH * 3 / 4), rand(self::IMG_HEIGHT * 1 / 4, self::IMG_HEIGHT * 3 / 4), $black);

        // Creo líneas de ancho, posición y dirección aleatorios
        for ($i = 0; $i < 2; $i ++) {
            imagesetthickness($this->image, rand(1, 2));
            imageline($this->image, 0, rand(0, self::IMG_HEIGHT), self::IMG_WIDTH - 1, rand(0, self::IMG_HEIGHT), $black);
        }

        // Establezco el ancho de linea y caracteres a los valores que voy a usar.
        imagesetthickness($this->image, 1);
        $charWidth = floor(self::IMG_WIDTH / self::CHARS_COUNT);

        // Ingreso caracteres rotados de forma aleatoria
        for ($index = 0; $index < strlen($this->code); $index ++) {
            $charImg = imagecreatetruecolor($charWidth, self::IMG_HEIGHT);
            $white = imagecolorallocate($charImg, 255, 255, 255);
            $transparent = imagecolortransparent($charImg, $white);
            imagefill($charImg, 0, 0, $transparent);

            $char = $this->code{$index};
            // Le ponemos un tamaño aleatorio (dentro de un rango) a los caracteres
            $char_height = rand(self::CHAR_MIN_HEIGHT, self::CHAR_MAX_HEIGHT);

            // Rotación con ángulo y sentido aleatorios
            $rotation_angle = rand(self::MIN_ROTATION, self::MAX_ROTATION);
            $clockwise = rand(0, 1);
            $rotation_angle -= ($clockwise ? 360 : 0);
            $rotation_angle = abs($rotation_angle);

            $charColor = imagecolorallocate($charImg, rand(0, 0x64), rand(0, 0x64), rand(0, 0x64));
            $shiftX = rand(0, self::MAX_SHIFT);
            $shiftX *= (rand(0, 1) ? 1 : - 1);
            $shiftY = rand(0, self::MAX_SHIFT);
            $shiftY *= (rand(0, 1) ? 1 : - 1);

            if (function_exists('imageantialias')) {
                imageantialias($charImg, rand(0, 1));
            }
            imagettftext($charImg, $char_height, $rotation_angle, self::MAX_SHIFT + $shiftX, $char_height + self::MAX_SHIFT * 2 + $shiftY, $charColor, rand(0, self::FONTS_COUNT - 1) . '.ttf', $char);
            imagerotate($charImg, $rotation_angle, $transparent, 1);

            // Filtro aleatorio
            if (rand(0, 1)) {
                imagefilter($charImg, rand(0, 1) ? IMG_FILTER_GAUSSIAN_BLUR : IMG_FILTER_MEAN_REMOVAL);
            }
            imagecopymerge($this->image, $charImg, $index * $charWidth, 0, 0, 0, $charWidth, self::IMG_HEIGHT, 100);
            imagefill($charImg, 0, 0, $transparent);

            imagedestroy($charImg);
        }

        // Color o blanco y negro, de forma aleatoria
        if (rand(0, 1)) {
            imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        }
        imageline($this->image, 0, rand(0, self::IMG_HEIGHT / 2), self::IMG_WIDTH - 1, rand(self::IMG_HEIGHT / 2, self::IMG_HEIGHT), $black);
    }
}