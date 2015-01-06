<?php
namespace Mcustiel\Captcha;

class NoFontsPathDefinedException extends CaptchaException
{
    public function __construct($message = 'There\'s not a fonts directory defined')
    {
        parent::__construct($message);
    }
}
