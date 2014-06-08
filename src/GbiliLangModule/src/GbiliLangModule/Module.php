<?php
namespace GbiliLangModule;

class Module 
{
    /**
     * Use this event if you want to manually set the textdomain
     * However if you only want to set the textdomain in case it
     * is missing, use \GbiliLangModule\Service\Textdomain::EVENT_MISSING_TEXTDOMAIN
     *
     * @see \GbiliLangModule\Service\Textdomain::EVENT_MISSING_TEXTDOMAIN
     *   to only set the textomain if it is missing
     * @var string
     */
    const EVENT_SET_TEXTDOMAIN = 'GbiliLangModule.textdomain_service.set_textdomain';

    /**
     * Textdomain injection must occur before lang injection
     */
    const TEXTDOMAIN_INJECTION_PRIORITY = 100;

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
        $this->injectTextdomainDontOverrideManualTextdomain($e);
        $this->injectLang($e);
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
        }, self::TEXTDOMAIN_INJECTION_PRIORITY - 1);
    }

    /**
     * onBoostrap call this method
     * Sets textdomain, may be overriden by other listeners
     */
    public static function setTextdomainManually(\Zend\Mvc\MvcEvent $e, $textdomain, $priority=1)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(\GbiliLangModule\Module::EVENT_SET_TEXTDOMAIN, function ($e) use ($textdomain) {
            $textdomainService = $e->getTarget();
            $textdomainService->setTextdomain($textdomain);
        }, $priority); // set priority to high negative numbers to override other listeners
    }

    /**
     * onBoostrap call this method 
     * Only sets textdomain if not set in self::EVENT_SET_TEXTDOMAIN
     */
    public static function setOnMissingTextdomain(\Zend\Mvc\MvcEvent $e, $textdomain, $priority=1)
    {
        $sm = $e->getApplication()->getServiceManager();
        $service = $sm->get('textdomain');
        $eventManager = $service->getEventManager();
        $eventManager->attach(\GbiliLangModule\Service\Textdomain::EVENT_MISSING_TEXTDOMAIN, function ($e) use ($textdomain) {
            $textdomainService = $e->getTarget();
            $textdomainService->setTextdomain($textdomain);
        }, $priority); // set priority to high negative numbers to override other listeners
    }

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

            $eventManager->trigger(self::EVENT_SET_TEXTDOMAIN, $service);

            if (!$service->hasTextdomain() && ($e->getTarget() instanceof \Zend\Mvc\Controller\AbstractActionController)) {
                $service->autosetGuessedTextdomain($e->getTarget());
            }
        }, self::TEXTDOMAIN_INJECTION_PRIORITY);
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
