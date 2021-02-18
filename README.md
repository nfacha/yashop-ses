Amazon ses extension for Yii2
=============================
Extension for sending emails via amazon ses. Part of nfacha

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nfacha/yii2-yashop-ses "*"
```

or add

```
"nfacha/yii2-yashop-ses": "*"
```

to the require section of your `composer.json` file.


Usage
-----

To use this extension, you should configure it in the application configuration like the following:

```php
'components' => [
    ...
    'mail' => [
        'class' => 'nfacha\ses\Mailer',
        'access_key' => 'Your access key',
        'secret_key' => 'Your secret key',
        'host' => 'email.us-east-1.amazonaws.com' // not required
    ],
    ...
],
```

To send an email, you may use the following code:

```php
Yii::$app->mail->compose('contact/html', ['contactForm' => $form])
    ->setFrom('from@domain.com')
    ->setTo($form->email)
    ->setSubject($form->subject)
    ->send();
```


To send an email with headers, you may use the following code:

```php
Yii::$app->mail->compose('contact/html', ['contactForm' => $form])
    ->setFrom('from@domain.com')
    ->setTo($form->email)
    ->setSubject($form->subject)
    ->setHeader('Precedence', 'bulk')
    ->setHeader('List-id', '<1>')
    ->setHeader('List-Unsubscribe', Url::to(['user/unsubscribe'], true))
    ->send();
```

Increase the speed of sending emails:

```php
Yii::$app->mailer->getSES()->enableVerifyHost(false);
Yii::$app->mailer->getSES()->enableVerifyPeer(false);
Yii::$app->mailer->getSES()->enableKeepAlive();

foreach ($emails as $email) {
    Yii::$app->mail->compose('delivery/mail', [])
        ->setFrom('from@domain.com')
        ->setTo($email)
        ->setSubject($subject)
        ->setHeader('Precedence', 'bulk')
        ->setHeader('List-id', '<1>')
        ->setHeader('List-Unsubscribe', Url::to(['user/unsubscribe'], true))
        ->send();
}

Yii::$app->mailer->getSES()->enableKeepAlive(false);
```
