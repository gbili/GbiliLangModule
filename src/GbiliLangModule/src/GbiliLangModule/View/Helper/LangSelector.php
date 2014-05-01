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
class LangSelector extends \Zend\View\Helper\AbstractHelper
{
    protected $availableLangs = null;
    protected $application = null;

    /**
     *
     * @return string
     */
    public function __invoke($param = null)
    {
        if (null !== $param) {
            return $this->handleParam($param);
        }
        return $this->getAvailableLangs();
    }

    public function handleParam($param)
    {
        return $this->$param();
    }

    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }

    public function getRouteMatch()
    {
        return $this->application->getMvcEvent()->getRouteMatch();
    }

    public function hasMatchedRoute()
    {
        return null !== $this->application->getMvcEvent()->getRouteMatch();
    }

    public function setAvailableLangs(array $langs)
    {
        $this->availableLangs = $langs;
        return $this;
    }

    public function getAvailableLangs()
    {
        if (null === $this->availableLangs) {
            throw new \Exception('Langs have not been injected');
        }
        return $this->availableLangs;
    }

    public function getDefaultPartialPath()
    {
        $scriptPath = __DIR__ . '/../../../../view/partial/' . $scriptName . '.phtml';
        if (!file_exists($scriptPath)) {
            throw new \Exception('The requested script does not exist');
        }
        return $scriptPath;

    }
}
