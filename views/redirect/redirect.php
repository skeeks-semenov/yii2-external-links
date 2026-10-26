<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */
/**
 * @var $this yii\web\View
 * @var $url string checked absolute http(s) link
 * @var $isSigned bool link was signed by this site
 */

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

\Yii::$app->externalLinks->enabled = false;
?>

<div style="text-align: center; padding: 100px 50px;">
    <?php if ($isSigned) : ?>
        <h1>Вы сейчас будете перенаправлены на страницу</h1>
        <h2><?= Html::a(Html::encode($url), $url, ['rel' => 'nofollow noopener']); ?></h2>
    <?php else : ?>
        <h1>Вы переходите на внешний сайт</h1>
        <p style="font-size: 1.5em;"><?= Html::encode((string) parse_url($url, PHP_URL_HOST)); ?></p>
        <p style="word-break: break-all; color: #777;"><?= Html::encode($url); ?></p>
        <p>Этот сайт не отвечает за содержимое внешних страниц. Продолжайте, только если доверяете этому адресу.</p>
        <p style="margin-top: 30px;">
            <?= Html::a('Перейти', $url, ['rel' => 'nofollow noopener noreferrer', 'class' => 'btn btn-primary']); ?>
            &nbsp;
            <?= Html::a('Вернуться на сайт', Url::home(), ['class' => 'btn btn-default']); ?>
        </p>
    <?php endif; ?>
</div>
<?php
if ($isSigned) {
    $jsUrl = Json::htmlEncode($url);
    $this->registerJs(<<<JS
setTimeout(function() {
    window.location.href = {$jsUrl};
}, 2000);
JS
    );
}
?>
