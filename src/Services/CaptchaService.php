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

        $options = json_decode($request->query->get('options'), true);

        $config['width'] = (int)$this->params->get('captcha_bundle.width') ?? 120;
        $config['height'] = (int)$this->params->get('captcha_bundle.height') ?? 40;
        $config['length'] = (int)$this->params->get('captcha_bundle.length') ?? 6;
        $config['lines'] = (int)$this->params->get('captcha_bundle.lines') ?? 8;
        $config['characters'] = $this->params->get('captcha_bundle.characters') ?? 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $config['case_sensitive'] = $this->params->get('captcha_bundle.case_sensitive');

        $config = array_replace_recursive($config, array_filter($options, fn ($value) => !is_null($value)));

        $this->validateConfig([
            'width' => $config['width'],
            'height' => $config['height'],
            'length' => $config['length'],
            'lines' => $config['lines'],
            'characters' => $config['characters'],
            'case_sensitive' => $config['case_sensitive']
        ]);

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
        $lineColor = imagecolorallocate($image, 100, 120, 180);

        // Generate a random light or dark background color
        $bgR = rand(180, 255);
        $bgG = rand(180, 255);
        $bgB = rand(180, 255);
        $bgColor = imagecolorallocate($image, $bgR, $bgG, $bgB);
        imagefill($image, 0, 0, $bgColor);

        // Calculate background luminance (perceived brightness)
        $luminance = (0.299 * $bgR + 0.587 * $bgG + 0.114 * $bgB) / 255;

        if ($luminance > 0.5) {
            // If background is light, use dark text
            $textColor = imagecolorallocate($image, rand(0, 50), rand(0, 50), rand(0, 50));
        } else {
            // If background is dark, use light text
            $textColor = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
        }

        // Add random lines
        for ($i = 0; $i < $config['lines']; $i++) {
            imagesetthickness($image, rand(1, 3));
            imageline($image, rand(0, $config['width']), rand(0, $config['height']), rand(0, $config['width']), rand(0, $config['height']), $lineColor);
        }

        // Add random dots
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand(0, $config['width']), rand(0, $config['height']), $lineColor);
        }

        // Calculate max width per character, leaving some margin
        $charCount = strlen($captchaText);
        $padding = max(5, $config['width'] * 0.05); // Ensure padding is at least 5px
        $availableWidth = $config['width'] - (2 * $padding);
        $maxCharWidth = floor($availableWidth / $charCount);

        // Calculate maximum font size based on both image height and available width
        // For instance, we use 70% of the image height and 80% of each character slot.
        $maxFontSize = (int) min($config['height'] * 0.7, $maxCharWidth * 0.8);
        if ($maxFontSize < 10) {
            $maxFontSize = 10;
        }
        // Define a lower bound for variation (80% of the max)
        $minFontSize = max(14, (int) ($maxFontSize * 0.8)); // Minimum font size is 14px

        // Add CAPTCHA text
        // Ensure font size doesn't exceed character width
        $x = (int) ($padding + ($maxCharWidth / 4)); // Small offset to center better

        $fontsPath = __DIR__ . '/../../assets/fonts/';
        $fonts = glob($fontsPath . '*.ttf');

        foreach (str_split($captchaText) as $char) {
            // Pick a random font for this character
            $fontFile = $fonts ? $fonts[array_rand($fonts)] : null;
            $fontSize = rand($minFontSize, $maxFontSize);
            $angle = rand(-20, 20);
            $y = rand($config['height'] / 2, $config['height'] - 10); // Keep text within the height range

            // Apply text with the selected font
            if ($fontFile) {
                imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $fontFile, $char);
            } else {
                imagestring($image, $x, 10, $y - 20, $char, $textColor);
            }

            // Adjust X position for the next character
            $x += (int)$maxCharWidth;
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
