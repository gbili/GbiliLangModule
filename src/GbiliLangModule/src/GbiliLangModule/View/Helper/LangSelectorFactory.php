<?php
namespace GbiliLangModule\View\Helper;

class LangSelectorFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $viewHelperPluginManager)
    {
        $sm = $viewHelperPluginManager->getServiceLocator();
        $config = $sm->get('config');
        $langs = $config['lang_selector']['langs_available'];

        $langSelector = new LangSelector;
        $langSelector->setAvailableLangs($langs);
        $langSelector->setApplication($sm->get('Application'));

        return $langSelector;
    }
}
