yii2-mail (under development!!!)
=

TODO: ADD DESCRIPTION

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
composer require mirkhamidov/yii2-mail "*"
```

or add

```
"mirkhamidov/yii2-mail": "*"
```

to the require section of your `composer.json` file.



Usage
-----

## Send email with attachment

```php
$_mailRecipient = 'you@email.com';
$_mailMoreData = [
    'someParamInEmail' => 'will be replaced with this text',
];
$_mailParams['attach'] = [
    [
        'file' => $model->params['file']['fileFullPath'],
        'params' => [
            // for details look at http://www.yiiframework.com/doc-2.0/yii-mail-messageinterface.html#attach()-detail
        ],
    ],
];

MMail::sendMail($_mailAlias, $_mailRecipient, $_mailMoreData, $_mailParams);
```
