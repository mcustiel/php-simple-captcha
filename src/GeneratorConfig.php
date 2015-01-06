<?php
namespace Mcustiel\Captcha;

class GeneratorConfig
{
    private $width = 300;
    private $height = 75;
    private $minRotation = 1;
    private $maxRotation = 15;
    private $charMinSize = 26;
    private $charMaxSize = 30;
    private $fontsPath = null;

    private function __construct()
    {
    }

    public static function create()
    {
        return new self;
    }

    public function withWidth($value)
    {
        $this->width = $value;
        return $this;
    }

    public function withHeight($value)
    {
        $this->height = $value;
        return $this;
    }

    public function withMinCharacterRotation($value)
    {
        $this->minRotation = $value;
        return $this;
    }

    public function withMaxCharacterRotation($value)
    {
        $this->maxRotation = $value;
        return $this;
    }

    public function withMinCharacterSize($value)
    {
        $this->charMinSize = $value;
        return $this;
    }

    public function withMaxCharacterSize($value)
    {
        $this->charMaxSize = $value;
        return $this;
    }

    public function withFontsPath($value)
    {
        $this->fontsPath = $value;
        return $this;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getMinCharacterRotation()
    {
        return $this->minRotation;
    }

    public function getMaxCharacterRotation()
    {
        return $this->maxRotation;
    }

    public function getMinCharacterSize()
    {
        return $this->charMinSize;
    }

    public function getMaxCharacterSize()
    {
        return $this->charMaxSize;
    }

    public function getFontsPath()
    {
        return $this->fontsPath;
    }
 }
