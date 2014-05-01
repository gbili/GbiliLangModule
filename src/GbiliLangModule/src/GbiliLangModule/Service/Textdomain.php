<?php
namespace GbiliLangModule\Service;

class Textdomain 
{
    protected $textdomain;
    protected $defaultTextdomain = 'application';

    protected $sm;

    public function __construct($sm = null)
    {
        if (null !== $sm) {
            $this->setServiceManager($sm);
        }
    }

    public function getTextdomain()
    {
        if (null !== $this->textdomain) {
            return $this->textdomain;
        }
        return $this->getDefaultTextdomain();
    }
    /**
     * @return manually set textdomain
     */
    public function setTextdomain($textdomain)
    {
        $this->textdomain = $textdomain;
        return $this;
    }

    public function getServiceManager()
    {
        if (null === $this->sm) {
            throw new \Exception('Sm not set');
        }
        return $this->sm;
    }

    public function setServiceManager($sm)
    {
        $this->sm = $sm;
        return $this;
    }

    public function getRegisteredModules()
    {
        $sm = $this->getServiceManager();
        $config = $sm->get('ApplicationConfig');
        return $config['modules'];
    }

    public function getTextdomains()
    {
        $textdomains = array_map(function ($modulename) {
            return strtolower($modulename);
        }, $this->getRegisteredModules());
        return $textdomains;
    }


    public function setController($controller)
    {
        if ($controller instanceof \Zend\Mvc\Controller\AbstractActionController) {
            $baseNamespace = current(explode('\\', get_class($controller)));
            if (in_array($baseNamespace, $this->getRegisteredModules())) {
                $this->textdomain = strtolower($baseNamespace);
            }
        }
    }

    public function getDefaultTextdomain()
    {
        $configTextdomain = $this->getConfigDefaultTextdomain();
        if (null !== $configTextdomain) {
            return $configTextdomain;
        }
        return $this->defaultTextdomain;
    }

    public function getConfigDefaultTextdomain()
    {
        $sm = $this->getServiceManager();
        $config = $sm->get('Config');
        if (isset($config['lang']) && isset($config['lang']['default_textdomain'])) {
            return $config;
        }
        return null;
    }
}
