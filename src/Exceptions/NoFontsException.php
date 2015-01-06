<?php
namespace Mcustiel\Captcha;

class NoFontsException extends CaptchaException
{
    public function __construct($message = 'The fonts directory does not contain any font')
    {
        parent::__construct($message);
    }
}
