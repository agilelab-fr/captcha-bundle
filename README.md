# AgilelabFr Symfony CAPTCHA Bundle

![Captcha Bundle in action](assets/images/captchaBundle.jpg)

---

![Packagist Downloads](https://img.shields.io/packagist/dt/agilelab-fr/captcha-bundle)

---

This bundle provides an easy way to integrate CAPTCHA validation into Symfony forms using the `AgilelabFrCaptchaType` form field. It enhances security by preventing automated submissions while ensuring a seamless user experience.

## Installation

### **For Symfony Flex Projects**
```
composer require agilelab-fr/captcha-bundle
```

After installation, you need to uncomment the following line in config/packages/agilelab_fr_captcha.yaml
```yaml
twig:
   form_themes:
      - '@AgilelabFrCaptcha/form/agilelab_fr_captcha.html.twig'
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

### **1. Configuration File (<small>`config/packages/agilelab_fr_captcha.yaml`</small>)**
```yaml
captcha_bundle:
    width: 120
    height: 40
    length: 6
    lines: 8
    characters: 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'
    case_sensitive: true

# Uncomment to render the twig block for captcha
twig:
    form_themes:
        - '@AgilelabFrCaptcha/form/agilelab_fr_captcha.html.twig'
```

### **2. Custom Config for each Form**

It is also possible to define the Captcha configuration directly in the `FormType`, in addition to the `config/packages/agilelab_fr_captcha.yaml` file.

Example usage in a form builder:

```php
->add('captcha', AgilelabFrCaptchaType::class, [
    'label' => 'Captcha',
    'attr' => [
        'width' => 120,
        'height' => 40,
        'lines' => 6,
        'length' => 5,
        'characters' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
        'case_sensitive' => false,
    ],
])
```

### **3. Routing (<small>`config/routes/agilelab_fr_captcha.yaml`</small>)**
```yaml
agilelab_fr_captcha_bundle.routes:
    resource: '@AgilelabFrCaptchaBundle/config/routes.yaml'
```

## License
This bundle is licensed under the MIT License.
