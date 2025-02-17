<?php

namespace AgilelabFr\CaptchaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgilelabFrCaptchaType extends AbstractType
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $captchaValue = $event->getData();
            $form = $event->getForm();

            $session = $this->requestStack->getSession();
            $storedCaptcha = $session->get('captcha'); // Retrieve stored CAPTCHA

            if (!$this->verifyCaptcha($captchaValue, $storedCaptcha)) {
                $form->addError(new FormError('Invalid CAPTCHA.'));
            }
        });
    }

    private function verifyCaptcha(string $input, string $storedCaptcha): bool
    {
        return $input === $storedCaptcha;
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
