<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 10.02.2016
 */
namespace skeeks\yii2\externalLinks;

use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Response;
use yii\web\View;

/**
 * Class ExternalLinksComponent
 * @package skeeks\yii2\externalLinks
 */
class ExternalLinksComponent extends Component implements BootstrapInterface
{
    const EVENT_BEFORE_PROCESSING = 'beforeProcessing';

    /**
     * @var bool
     */
    public $enabled = true;

    /**
     * Do not change the links in which there are domain names
     * @var array
     */
    public $noReplaceLinksOnDomains = [
        //'skeeks.com',
        //'www.skeeks.com',
    ];

    /**
     * @var bool Do not change absolute references to the domain name obtained from the information \Yii::$app->request->hostInfo
     */
    public $noReplaceLocalDomain = true;

    /**
     * The base of the new url
     * @var string
     */
    public $backendRoute = '/externallinks/redirect/redirect';

    /**
     * @var string
     */
    public $backendRouteParam = 'url';

    /**
     * @var bool Include links in the coding b64_encode
     */
    public $enabledB64Encode = true;

    /**
     * @param \yii\web\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof Application)
        {
            $app->response->on(Response::EVENT_AFTER_PREPARE, function(Event $e) use ($app)
            {
                /**
                 * @var $response Response
                 */
                $response = $e->sender;

                $this->trigger(self::EVENT_BEFORE_PROCESSING);

                if ($this->enabled && !$app->request->isAjax && !$app->request->isPjax && $app->response->format == Response::FORMAT_HTML)
                {
                    \Yii::beginProfile('ExternalLinks');

                    $content = $response->content;
                    $this->initReplaceLinks();

                    $matches = [];
                    if (preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $content, $matches))
                    {
                        if (isset($matches[1]))
                        {
                            foreach ($matches[1] as $link)
                            {
                                //Относительные ссылки пропускать
                                if (Url::isRelative($link))
                                {
                                    continue;
                                }

                                if ($dataLink = parse_url($link))
                                {
                                    //Для этого хоста не нужно менять ссылку
                                    $host = ArrayHelper::getValue($dataLink, 'host');
                                    if (in_array($host, $this->noReplaceLinksOnDomains))
                                    {
                                        continue;
                                    }
                                }

                                $linkForUrl = $link;
                                if ($this->enabledB64Encode)
                                {
                                    $linkForUrl = base64_encode($link);
                                }

                                $newUrl = Url::to([$this->backendRoute, $this->backendRouteParam => $linkForUrl]);

                                $replaceUrl = 'href="' . $newUrl . '"';
                                $content = str_replace('href="' . $link . '"', $replaceUrl, $content);

                                $replaceUrl = 'href=\'' . $newUrl . '\'';
                                $content = str_replace('href=\'' . $link . '\'', $replaceUrl, $content);
                            }

                        }
                    }

                    $response->content = $content;

                    \Yii::endProfile('ExternalLinks');
                }
            });
        }
    }

    /**
     * @return $this
     */
    protected function initReplaceLinks()
    {
        if ($this->noReplaceLocalDomain && \Yii::$app->request->hostInfo)
        {
            if ($dataLink = parse_url(\Yii::$app->request->hostInfo))
            {
                //Для этого хоста не нужно менять ссылку
                $host = ArrayHelper::getValue($dataLink, 'host');
                $this->noReplaceLinksOnDomains[] = $host;
            }
        }
        return $this;
    }
}
