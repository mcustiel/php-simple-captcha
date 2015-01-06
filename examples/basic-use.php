<?php
require 'vendor/autoload.php';

use Mcustiel\Captcha\CaptchaGenerator;
use Mcustiel\Captcha\Captcha;

define('SIMPLECAPTCHA_FONTS_PATH', __DIR__ . '/tests/fixtures/fonts/');

$captcha = new Captcha();

$code = $captcha->getCode();
$_SESSION['captcha-code'] = $code;

$captcha->sendImageToBrowser();
$captcha->destroy();
