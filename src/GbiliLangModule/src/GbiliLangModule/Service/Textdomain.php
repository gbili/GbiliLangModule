<?php
namespace GbiliLangModule\Service;

class Textdomain implements \Zend\EventManager\EventManagerAwareInterface
{
    const EVENT_MISSING_TEXTDOMAIN = 'GbiliLangModule\Service\Textdomain.missing_texdomain';

    protected $textdomain;

    protected $defaultTextdomain = 'application';

    protected $sm;

    protected $controller;

    /**
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $eventManager;

    public function __construct($sm = null)
    {
        if (null !== $sm) {
            $this->setServiceManager($sm);
        }
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $eventManager
     */
    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function getTextdomain()
    {
        if (!$this->hasTextdomain()) {
            $this->eventManager->trigger(self::EVENT_MISSING_TEXTDOMAIN, $this);
            if (!$this->hasTextdomain()) {
                throw new \Exception('Missing textdomain, no listeners set it');
            }
        }
        return $this->textdomain;
    }

    public function hasTextdomain()
    {
        return null !== $this->textdomain;
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
        $inflector = new \Zend\Filter\Word\CamelCaseToDash();
        $textdomains = array_map(function ($modulename) use ($inflector) {
            return strtolower($inflector->filter($modulename));
        }, $this->getRegisteredModules());
        return $textdomains;
    }


    public function setController($controller)
    {
        if ($controller instanceof \Zend\Mvc\Controller\AbstractActionController) {
            $this->controller = $controller;
        }
        return $this;
    }

    public function hasController()
    {
        return null !== $this->controller;
    }

    public function getController()
    {
        if (!$this->hasController()) {
            throw new \Exception('No controller was set');
        }
        return $this->controller;
    }

    public function canGuessTextdomain()
    {
        return $this->hasController();
    }

    public function guessTexdomain()
    {
        if (!$this->canGuessTextdomain()) {
            throw new \Exception('Cannot guess textdomain if no controller is set');
        }
        $baseNamespace = current(explode('\\', get_class($this->getController())));
        return strtolower($baseNamespace);
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
