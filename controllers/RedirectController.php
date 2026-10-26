<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 10.02.2016
 */

namespace skeeks\yii2\externalLinks\controllers;
use skeeks\yii2\externalLinks\ExternalLinksComponent;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Transition page to an external link.
 *
 * Only absolute http(s) links are accepted. A link signed by this site (see ExternalLinksComponent::createRedirectUrl())
 * is opened automatically; an unsigned link (old caches, search engines or a foreign substitution) is shown with a button only.
 *
 * Class RedirectController
 * @package skeeks\yii2\externalLinks\controllers
 */
class RedirectController extends Controller
{
    public $defaultAction = 'redirect';

    public function actionRedirect()
    {
        /**
         * @var $component ExternalLinksComponent
         */
        $component = \Yii::$app->externalLinks;
        $request = \Yii::$app->request;

        \Yii::$app->response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $this->view->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow'], 'robots');

        $url = $request->get($component->backendRouteParam);
        if (!is_string($url) || $url === '') {
            throw new BadRequestHttpException;
        }

        if ($component->enabledB64Encode) {
            $url = base64_decode($url, true);
        }

        if (!$component->isAllowedUrl($url)) {
            throw new BadRequestHttpException;
        }

        $isSigned = $component->validateSign($url, $request->get($component->signParam));

        \Yii::info("redirrect to: " . $url . ($isSigned ? "" : " (unsigned)"), self::class);

        return $this->render($this->action->id, [
            'url'      => $url,
            'isSigned' => $isSigned,
        ]);
    }
}
