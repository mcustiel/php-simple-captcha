<?php
namespace Mcustiel\Captcha;

class Captcha
{
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

    private $validAsciiRanges = array(
        array(48, 57), // Digits
        array(65, 90) // Uppercase letters
    );

    public function __construct($code = null, GeneratorConfig $config = null)
    {
        $this->code = $code;
        $this->generator = new CaptchaGenerator(
            $config === null ? GeneratorConfig::create() : $config
        );
    }

    public function getCode()
    {
        if ($this->code === null) {
            $this->code = $this->generateCode(5);
        }

        return $this->code;
    }

    public function generateCode($size)
    {
        $code = '';
        for ($i = 0; $i < $size; $i ++) {
            $index = rand(0, count($this->validAsciiRanges) - 1);
            $code .= chr(
                rand($this->validAsciiRanges[$index][0], $this->validAsciiRanges[$index][1])
            );
        }
        return $code;
    }

    public function sendImageToFile($file)
    {
        $this->generateImageResource();

        imagepng($this->image, $file);
    }

    public function sendImageToBrowser()
    {
        header('Content-type', 'image/png');
        $this->sendImageToFile(null);
    }

    public function destroy()
    {
        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }
    }

    private function generateImageResource()
    {
        if (!is_resource($this->image)) {
            $this->image = $this->generator->generate($this->getCode());
        }
    }
}
