<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace GbiliLangModule\View\Helper;

/**
 * View helper for translating messages.
 */
class Lang extends \Zend\View\Helper\AbstractHelper
{
    private $lang;

    /**
     * @return string
     */
    public function __invoke($param = null)
    {
        if (null !== $param) {
            return $this->handleParam($param);
        }
        return $this->lang;
    }

    public function handleParam($param)
    {
        if ($param instanceof \Zend\Navigation\Page\Mvc) {
            $this->injectLangAsPageRouteParam($param);
        } else {
            throw new \Exception("Parameter not supported");
        }
    }

    public function injectLangAsPageRouteParam($page)
    {
        $page->setParams(array_merge(array('lang' => $this->lang), $page->getParams()));
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getLang()
    {
        return $this->lang;
    }
}
