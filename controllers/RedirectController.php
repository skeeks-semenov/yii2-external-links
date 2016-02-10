<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 10.02.2016
 */

namespace skeeks\yii2\externalLinks\controllers;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class RedirectController
 * @package skeeks\yii2\externalLinks\controllers
 */
class RedirectController extends Controller
{
    public $defaultAction = 'redirect';

    public function actionRedirect()
    {
        if ($url = \Yii::$app->request->get(\Yii::$app->externalLinks->backendRouteParam))
        {
            if (\Yii::$app->externalLinks->enabledB64Encode)
            {
                $url = base64_decode($url);
            }

            return $this->redirect($url);
        }

        throw new BadRequestHttpException;
    }
}