<?php

namespace AgilelabFr\CaptchaBundle\Controller;

use AgilelabFr\CaptchaBundle\Services\CaptchaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CaptchaController extends AbstractController
{
    public function __construct(private readonly CaptchaService $captchaService)
    {
    }

    #[Route('/agilelab_fr/captchabundle/generate-captcha', name: 'agilelabfr_generate_captcha')]
    public function generateCaptcha(Request $request): Response
    {

        try {
            $imageData = $this->captchaService->generateCaptcha($request);
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

        // Return response
        return new Response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
