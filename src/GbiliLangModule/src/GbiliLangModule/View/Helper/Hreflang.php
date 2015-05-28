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
 * In multilang websites, pages have translations
 * in other langs. This helper returns the seo
 * optimized links to those translations.
 */
class Hreflang extends \Zend\View\Helper\AbstractHelper
{
    /**
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->hreflang();
    }

    public function hreflang()
    {
        $hreflangs = '';
        foreach ($this->view->langSelector()->getAvailableLangs() as $lang) {
            if ($this->view->lang() === $lang) continue;
            $hreflangs .= $this->view->headLink([
                'rel' => 'alternate', 
                'hreflang' => $lang, 
                'href' => $this->view->url(null, ['lang' => $lang], ['force_canonical' => true],true)
            ,]);
        }
        return $hreflangs;
    }
}
