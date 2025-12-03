<?php
session_start();

$width = 120;
$height = 40;
$length = 5;

$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$captcha_text = '';
for ($i = 0; $i < $length; $i++) {
    $captcha_text .= $characters[random_int(0, strlen($characters) - 1)];
}

$_SESSION['captcha_text'] = $captcha_text;

$image = imagecreatetruecolor($width, $height);
$bg_color = imagecolorallocate($image, 240, 240, 240);
$text_color = imagecolorallocate($image, 50, 50, 50);
$line_color = imagecolorallocate($image, 180, 180, 180);

imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

for ($i = 0; $i < 4; $i++) {
    imageline($image, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $line_color);
}

$font_size = 20;
$x = 10;
$y = 28;

imagestring($image, 5, $x, $height - 30, $captcha_text, $text_color);

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>