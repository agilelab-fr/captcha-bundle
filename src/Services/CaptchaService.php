<?php

namespace AgilelabFr\CaptchaBundle\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

class CaptchaService
{
    public function __construct(
        private ParameterBagInterface $params,
    ) {
    }

    public function generateCaptcha(Request $request): false|string
    {
        $parameters = $this->params->get('agilelabfr_captcha');
        $width = $parameters['width'] ?? 120;
        $height = $parameters['height'] ?? 40;
        $length = $parameters['length'] ?? 6;
        $lines = $parameters['lines'] ?? 8;
        $characters = $parameters['characters'] ?? 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        // Generate random CAPTCHA text
        $captchaText = '';
        for ($i = 0; $i < $length; $i++) {
            $captchaText .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Store CAPTCHA text in session
        $session = $request->getSession();
        $session->set('captcha', $captchaText);

        // Create original CAPTCHA image
        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, 230, 230, 230);
        $textColor = imagecolorallocate($image, 20, 40, 100);
        $lineColor = imagecolorallocate($image, 100, 120, 180);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Add random lines
        for ($i = 0; $i < $lines; $i++) {
            imagesetthickness($image, rand(1, 3));
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }

        // Add random dots
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), $lineColor);
        }

        // Add CAPTCHA text
        $fontSize = rand(18, 22);
        $x = rand(10, 20);
        $y = rand(25, 35);
        $fontPath = __DIR__.'/../../assets/fonts/Roboto-VariableFont.ttf';

        if (!file_exists($fontPath)) {
            imagestring($image, 5, $x, $y - 10, 'ERR', $textColor);
        } else {
            imagettftext($image, $fontSize, rand(-10, 10), $x, $y, $textColor, $fontPath, $captchaText);
        }

        // Capture output
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();

        // Free memory
        imagedestroy($image);

        return $imageData;
    }

}
