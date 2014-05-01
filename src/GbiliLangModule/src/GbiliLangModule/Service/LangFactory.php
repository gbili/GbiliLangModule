<?php
namespace GbiliLangModule\Service;

class LangFactory implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    {
        $lang = new Lang($sm->get('Application'));
        return $lang;
    }
}
