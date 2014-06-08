<?php
namespace GbiliLangModule;

class Module 
{
    const EVENT_SET_TEXTDOMAIN = 'GbiliLangModule.textdomain_service.set_textdomain';

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
        $this->injectTextdomainDontOverrideManualTextdomain($e);
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

    /**
     * onBoostrap call this method (copy its contents to your module)
     * Ex: $this->manualTextdomain('my-module', \Zend\Mvc\MvcEvent $e)
     */
    /*
    public function manualTextdomain($textdomain, \Zend\Mvc\MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(\GbiliLangModule\Module::EVENT_SET_TEXTDOMAIN, function ($e) use ($textdomain) {
            $textdomainService = $e->getTarget();
            $textdomainService->setTextdomain($textdomain);
        }, 1); // set priority to high negative numbers to override other listeners
    }*/

    /**
     * Set the textdomain according to the controller being dispatched
     * only if it is not already set from event listeners to self::EVENT_SET_TEXTDOMAIN
     */
    public function injectTextdomainDontOverrideManualTextdomain($e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, function ($e) {
            $app = $e->getApplication();
            $sm = $app->getServiceManager();
            $eventManager = $app->getEventManager();

            $service = $sm->get('textdomain');

            $eventManager->trigger(self::EVENT_SET_TEXDOMAIN, $service);

            if (!$service->hasTextdomain()) {
                $service->setController($e->getTarget());
            }
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
