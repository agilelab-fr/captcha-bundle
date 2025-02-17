# AgilelabFr CAPTCHA Bundle

This bundle provides an easy way to integrate CAPTCHA validation into Symfony forms using the `AgilelabFrCaptchaType` form field. It enhances security by preventing automated submissions while ensuring a seamless user experience.

## Installation

### **For Symfony Flex Projects**
```
composer require agilelab-fr/captcha-bundle
```

### **For Non-Flex Projects**
1. Install via Composer:
   ```
   composer require agilelab-fr/captcha-bundle
   ```
2. Manually enable the bundle in `config/bundles.php`:
   ```php
   return [
       AgilelabFr\CaptchaBundle\AgilelabFrCaptchaBundle::class => ['all' => true],
   ];
   ```
3. Add the configuration manually (see below).

## Usage

To add a CAPTCHA field to your form:

```php
use AgilelabFr\CaptchaBundle\Form\AgilelabFrCaptchaType;

$builder->add('captcha', AgilelabFrCaptchaType::class);
```

## Configuration

If you are **not using Symfony Flex**, manually add these configurations:

### **1. Configuration File (`config/packages/agilelab_fr_captcha.yaml`)**
```yaml
agilelabfr_captcha:
    width: 120
    height: 40
    length: 6
    lines: 8
    characters: 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'

twig:
    form_themes:
        - '@AgilelabFrCaptcha/form/agilelab_fr_captcha.html.twig'
```

### **2. Routing (`config/routes/agilelab_fr_captcha.yaml`)**
```yaml
agilelab_fr_captcha_bundle.routes:
    resource: '@AgilelabFrCaptchaBundle/config/routes.yaml'
```

## License
This bundle is licensed under the MIT License.
