php-simple-captcha
==================

A minimalist library that generates random captcha images using GD2.

Basic usage
===========

PhpSimpleCaptcha has a preset configuration that works pretty well, check GeneratorConfig to check the customizations available.
Next example shows how to use SimpleCaptcha with it's default configuration and autogenerating the code.

```PHP
<?php

use Mcustiel\Captcha\Captcha;

define('SIMPLECAPTCHA_FONTS_PATH', '/usr/local/fonts/ttf/');

$captcha = new Captcha();

$code = $captcha->getCode();
$_SESSION['captcha-code'] = $code;

$captcha->sendImageToBrowser();
$captcha->destroy();
```
