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
     * @var string Name of the GET parameter with the link signature
     */
    public $signParam = 'sign';

    /**
     * @var string|null Secret for link signatures; request cookieValidationKey by default
     */
    public $signKey;

    /**
     * @var string[] Only these schemes are accepted by the redirect page
     */
    public $allowedSchemes = ['http', 'https'];

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
                    if (preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", (string) $content, $matches))
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

                                if (!$this->isAllowedUrl(Html::decode($link)))
                                {
                                    continue;
                                }

                                $newUrl = Html::encode($this->createRedirectUrl(Html::decode($link)));

                                //replacing references only after <body
                                $bodyPosition   = strpos($content, '<body');
                                $headerContent  = substr($content, 0, $bodyPosition);
                                $bodyContent    = substr($content, $bodyPosition, strlen($content));

                                $resultContent = '';

                                $replaceUrl = 'href="' . $newUrl . '"';
                                $bodyContent = str_replace('href="' . $link . '"', $replaceUrl, $bodyContent);

                                $replaceUrl = 'href=\'' . $newUrl . '\'';
                                $bodyContent = str_replace('href=\'' . $link . '\'', $replaceUrl, $bodyContent);

                                $resultContent = $headerContent . $bodyContent;
                                $content = $resultContent;
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
     * Redirect page url for an external link, signed by this site
     *
     * @param string $url
     * @return string
     */
    public function createRedirectUrl($url)
    {
        return Url::to([
            $this->backendRoute,
            $this->backendRouteParam => $this->enabledB64Encode ? base64_encode($url) : $url,
            $this->signParam         => $this->sign($url),
        ]);
    }

    /**
     * @param string $url
     * @return string
     */
    public function sign($url)
    {
        $key = $this->signKey ?: (string) \Yii::$app->request->cookieValidationKey;
        return substr(hash_hmac('sha256', 'externalLinks|' . $url, $key), 0, 32);
    }

    /**
     * @param string $url
     * @param string|null $sign
     * @return bool
     */
    public function validateSign($url, $sign)
    {
        return is_string($sign) && $sign !== '' && hash_equals($this->sign($url), $sign);
    }

    /**
     * Only absolute links with an allowed scheme and a host may be opened by the redirect page
     *
     * @param string $url
     * @return bool
     */
    public function isAllowedUrl($url)
    {
        if (!is_string($url) || $url === '' || strlen($url) > 4096 || preg_match('/[\x00-\x20\x7f\\\\]/', $url))
        {
            return false;
        }

        $data = parse_url($url);
        if (!$data || empty($data['host']) || empty($data['scheme']))
        {
            return false;
        }

        return in_array(strtolower($data['scheme']), $this->allowedSchemes, true);
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
