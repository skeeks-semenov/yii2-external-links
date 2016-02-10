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
    'bootstrap'    => ['assetsAutoCompress'],
    'components'    =>
    [
    //....
        'assetsAutoCompress' =>
        [
            'class'         => '\skeeks\yii2\assetsAuto\AssetsAutoCompressComponent',
        ],
    //....
    ]
]

```



```php
//App config
[
    'bootstrap'    => ['assetsAutoCompress'],
    'components'    =>
    [
    //....
        'assetsAutoCompress' =>
        [
            'class'             => '\skeeks\yii2\assetsAuto\AssetsAutoCompressComponent',
            'enabled'           => true,
            'jsCompress'        => true,
            'cssFileCompile'    => true,
            'jsFileCompile'     => true,
        ],
    //....
    ]
]

```



> [![skeeks!](https://gravatar.com/userimage/74431132/13d04d83218593564422770b616e5622.jpg)](http://www.skeeks.com)  
<i>Web development has never been so fun!</i>  
[www.skeeks.com](http://www.skeeks.com)
