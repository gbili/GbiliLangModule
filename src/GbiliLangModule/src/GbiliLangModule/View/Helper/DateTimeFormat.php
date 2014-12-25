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
class DateTimeFormat extends \Zend\View\Helper\AbstractHelper
{
    protected $service;

    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->service->getDateTimeFormat();
    }

    public function setService(\GbiliLangModule\Service\Lang $lang)
    {
        $this->service = $lang;
        return $this;
    }
}
