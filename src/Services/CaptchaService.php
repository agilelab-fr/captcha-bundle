<?php

namespace AgilelabFr\CaptchaBundle\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CaptchaService
{
    private ParameterBagInterface $params;
    private ValidatorInterface $validator;

    public function __construct(ParameterBagInterface $params, ValidatorInterface $validator)
    {
        $this->params = $params;
        $this->validator = $validator;
    }

    public function validateConfig(array $config): void
    {
        $constraints = new Collection([
            'width' => [new NotBlank(), new Positive()],
            'height' => [new NotBlank(), new Positive()],
            'length' => [new NotBlank(), new Positive()],
            'lines' => [new NotBlank(), new Positive()],
            'characters' => [new NotBlank(), new Length(['min' => 6])],
            'case_sensitive' => [new Type('boolean')]
        ]);

        $errors = $this->validator->validate($config, $constraints);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new ValidationFailedException(implode(', ', $errorMessages), $errors);
        }

    }

    public function generateCaptcha(Request $request): string
    {

        $config['width'] = (int)$this->params->get('captcha_bundle.width') ?? 120;
        $config['height'] = (int)$this->params->get('captcha_bundle.height') ?? 40;
        $config['length'] = (int)$this->params->get('captcha_bundle.length') ?? 6;
        $config['lines'] = (int)$this->params->get('captcha_bundle.lines') ?? 8;
        $config['characters'] = $this->params->get('captcha_bundle.characters') ?? 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $config['case_sensitive'] = $this->params->get('captcha_bundle.case_sensitive');

        $this->validateConfig($config);

        // Generate random CAPTCHA text
        $captchaText = '';
        for ($i = 0; $i < $config['length']; $i++) {
            $captchaText .= $config['characters'][rand(0, strlen($config['characters']) - 1)];
        }

        // Store CAPTCHA text in session
        $session = $request->getSession();
        $session->set('captcha', $captchaText);

        // Create original CAPTCHA image
        $image = imagecreatetruecolor($config['width'], $config['height']);
        $bgColor = imagecolorallocate($image, 230, 230, 230);
        $textColor = imagecolorallocate($image, 20, 40, 100);
        $lineColor = imagecolorallocate($image, 100, 120, 180);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Add random lines
        for ($i = 0; $i < $config['lines']; $i++) {
            imagesetthickness($image, rand(1, 3));
            imageline($image, rand(0, $config['width']), rand(0, $config['height']), rand(0, $config['width']), rand(0, $config['height']), $lineColor);
        }

        // Add random dots
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand(0, $config['width']), rand(0, $config['height']), $lineColor);
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
