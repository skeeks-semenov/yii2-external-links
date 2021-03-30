<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */
/**
* @var $this yii\web\View
*/

\Yii::$app->externalLinks->enabled = false;
?>

<div style="text-align: center; padding: 100px 50px;">
    <h1>Вы сейчас будете перенаправлены на страницу</h1>
    <h2><a href="<?php echo $url; ?>"><?php echo $url; ?></a></h2>
</div>
<?php
$this->registerJs(<<<JS
setTimeout(function() {
    window.location.href = "{$url}";
}, 2000);
JS
);
?>