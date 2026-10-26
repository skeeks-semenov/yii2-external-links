AutoCorrect external links after rendering html page
===================================

[![Latest Stable Version](https://img.shields.io/packagist/v/skeeks/yii2-external-links.svg)](https://packagist.org/packages/skeeks/yii2-external-links)
[![Total Downloads](https://img.shields.io/packagist/dt/skeeks/yii2-external-links.svg)](https://packagist.org/packages/skeeks/yii2-external-links)

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


How to use (simple)
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

How to use (advanced)
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
        ],
        
        'urlManager' => 
        [
            'rules' => 
            [
                //Rewriting the standard route
                '~skeeks-redirect'                        => '/externallinks/redirect/redirect',
            ]
        ]
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

Redirect page security
----------

* Only absolute links with a scheme from `allowedSchemes` (`http`, `https` by default) are accepted; anything else gets 400.
* Links replaced on the page are signed: `createRedirectUrl($url)` adds the `signParam` (`sign`) parameter, an HMAC of the link
  with `signKey` (the request `cookieValidationKey` by default). Signed links are opened automatically.
* Unsigned or wrongly signed links (old caches, search engines, links made by somebody else) are not opened automatically:
  the page shows the target address and a "go" button.
* The page is sent with `X-Robots-Tag: noindex, nofollow` and the robots meta tag. Do not disallow it in robots.txt,
  otherwise search engines cannot see noindex.

##Screenshot
[![SkeekS CMS admin panel](http://marketplace.cms.skeeks.com/uploads/all/b3/c5/f6/b3c5f64a07798c80f78c0de102a4cf14.png)](http://marketplace.cms.skeeks.com/uploads/all/b3/c5/f6/b3c5f64a07798c80f78c0de102a4cf14.png)


___

> [![skeeks!](https://skeeks.com/img/logo/logo-no-title-80px.png)](https://skeeks.com)  
<i>SkeekS CMS (Yii2) — quickly, easily and effectively!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)



