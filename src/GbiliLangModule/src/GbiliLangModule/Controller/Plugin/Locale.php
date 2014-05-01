<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace GbiliLangModule\Controller\Plugin;

/**
 *
 */
class Locale extends \Zend\Mvc\Controller\Plugin\AbstractPlugin
{
    protected $locale;

    /**
     * 
     * @return string
     */
    public function __invoke()
    {
        if (null === $this->locale) {
	        $this->locale = $this->getController()->getServiceLocator()->get('lang')->getLang();
        } 
        return $this->locale;
    }
}
