<?php
namespace GbiliLangModule;

class Module 
{
    public function getConfig()
    {
        $preConfig = include __DIR__ . '/../../config/module.pre_config.php';
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        //$this->populateTranslations($e);
        $this->injectLang($e);
        $this->manualTextdomain('lang', $e);//$this->injectTextdomain($e);
        $this->missingTranslationListener($e);
    }

    public function missingTranslationListener($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('MvcTranslator');
        $translator->enableEventManager();
        $eventManager = $translator->getEventManager();
        $eventManager->attach(\Zend\I18n\Translator\Translator::EVENT_MISSING_TRANSLATION, function ($e) use ($sm){ 
            $params                    = $e->getParams();
            $translator                = $e->getTarget();
            $translationStorageService = $sm->get('translationStorage');
            $translationStorageService->setTranslation($params['text_domain'], $params['locale'], $params['message'], $translation='', $overwrite=false);
            $translationStorageService->persistFlushCache();
        });
    }
    
    public function injectLang($e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, function ($e) {
            $sm = $e->getApplication()->getServiceManager();
            $defaultLang = 'en';
            $langService = $sm->get('lang');
            $translator  = $sm->get('MvcTranslator');
            $currentLang = $langService->getLang();

            $translator->setFallbackLocale($defaultLang);
            $langService->setDefault($defaultLang);
            $translator->setLocale($currentLang);
        });
    }

    public function manualTextdomain($textdomain, $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $textdomainService = $sm->get('textdomain');
        $textdomainService->setTextdomain($textdomain);
    }

    public function injectTextdomain($e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, function ($e) {
            $sm = $e->getApplication()->getServiceManager();
            $textdomainService = $sm->get('textdomain');
            $textdomainService->setController($e->getTarget());
        });
    }

    /**
     * Get backedmodue translations and populate lang translations module with them
     */
    public function populateTranslations($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $translationStorageService = $sm->get('translationStorage');
        $langService = $sm->get('lang');
        $textdomainToPopulate = 'lang';
        $textdomainService = $sm->get('textdomain');
        foreach ($langService->getLangsAvailable() as $lang) {
            foreach ($translationStorageService->getTranslations($textdomainToPopulate, $lang) as $string => $translation) {
                foreach ($textdomainService->getTextdomains() as $textdomain) {
                    echo "toPop: $textdomainToPopulate, lang: $lang,  string: $string, toPopTranslation: $translation\n";
                    //If no translation available skip
                    if (!$translationStorageService->isTranslated($textdomain, $lang, $string)) 
                        continue;
                    //If textdomainToPopulate has already a translation, skip
                    if ($translationStorageService->isTranslated($textdomainToPopulate, $lang, $string)) 
                        continue;
                    $translation = $translationStorageService->getTranslation($textdomain, $lang, $string);
                    $translationStorageService->setTranslation($textdomainToPopulate, $lang, $string, $translation);
                }
            }
        }
        $translationStorageService->persistFlushCache();
    }
}
