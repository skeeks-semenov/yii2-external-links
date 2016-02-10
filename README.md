AutoCorrect external links after rendering html page
===================================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist skeeks/yii2-external-links "*"
```

or add

```
"skeeks/yii2-external-links": "*"
```


How to use
----------


```php
//App config
[
    'bootstrap'    => ['externalLinks'],

    'components'    =>
    [
        //....
        'externalLinks' =>
        [
            'class' => 'skeeks\yii2\externalLinks\ExternalLinksComponent',

            //Additional
            'enabled'                           => true,
            'noReplaceLocalDomain'              => true,
            'backendRoute'                      => '/externallinks/redirect/redirect',
            'backendRouteParam'                 => 'url',
            'enabledB64Encode'                  => true,
            'noReplaceLinksOnDomains'           => [
                'site1.ru',
                'www.site1.ru',
                'site2.ru',
            ],
            'callback' => function(skeeks\yii2\externalLinks\ExternalLinksComponent $component)
            {
                if (\Yii::$app->request->get('test'))
                {
                    $component->enabled = false;
                }
                $component->noReplaceLinksOnDomains[] = 'test.ru';
            }
        ],
        //....
    ],

    'modules'    =>
    [
        //....
        'externallinks' =>
        [
            'class' => 'skeeks\yii2\externalLinks\ExternalLinksModule',
        ],
        //....
    ]
]

```



> [![skeeks!](https://gravatar.com/userimage/74431132/13d04d83218593564422770b616e5622.jpg)](http://skeeks.com)
<i>SkeekS CMS (Yii2) � ������, ������, ����������!</i>
[skeeks.com](http://skeeks.com) | [cms.skeeks.com](http://cms.skeeks.com) | [marketplace.cms.skeeks.com](http://marketplace.cms.skeeks.com)

