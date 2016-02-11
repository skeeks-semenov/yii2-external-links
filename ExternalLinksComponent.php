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
     *
     * Additional logic to disable AutoCorrect. For example, if running the admin part, or is there the option not to include the AutoCorrect
     *
     *  function(ExternalLinksComponent $component)
     *  {
     *     if (\Yii::$app->request->get('test'))
     *      {
     *          $component->enabled = false;
     *      }
     *   
     *      if (\Yii::$app->cms->moduleAdmin->requestIsAdmin())
     *      {
     *          $component->enabled = false;
     *      }
     *
     *   $component->noReplaceLinksOnDomains[] = 'test.ru';
     *  }
     * @var callable
     */
    public $callback = null;



    /**
     * @param \yii\web\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof Application)
        {
            $app->view->on(View::EVENT_END_PAGE, function(Event $e)
            {
                $callback = $this->callback;
                if ($callback && is_callable($callback))
                {
                    $callback($this);
                }

                if ($this->enabled === false)
                {
                    return false;
                }

                /**
                 * @var $view View
                 */
                $view = $e->sender;
                if ($this->enabled && $view instanceof View && \Yii::$app->response->format == Response::FORMAT_HTML && !\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax && \Yii::$app->response->statusCode == 200)
                {
                    \Yii::beginProfile('ExternalLinks');

                    $content = ob_get_clean();
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

                    /*$pattern = '#(<a[a-z\-_\s\"\#\=]*)(href=")((https?|ftp)://)#i';
                    $replace = '$1 target="_blank" $2/~trust-redirect?url=$3';
                    $content = preg_replace($pattern, $replace, $content);*/

                    echo $content;
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
