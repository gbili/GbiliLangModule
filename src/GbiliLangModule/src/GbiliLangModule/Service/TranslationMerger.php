<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace GbiliLangModule\Service;

/**
 * Get all the translation files and merge them into a single file per language under
 * this module's textdomain
 */
class TranslationMerger
{
    /**
     * @var \GbiliLangModule\Service\TranslationStorage
     */
    protected $translationStorageService;

    /**
     * @var \GbiliLangModule\Service\Lang
     */
    protected $langService;

    /**
     * @var \GbiliLangModule\Service\Textdomain
     */
    protected $textdomainService;

    public function mergeAllTextdomainTranslations($mergeIntoTextdomain = 'lang')
    {
        return $this->mergeTextdomainsTranslations($this->getTextdomainService()->getTextdomains(), $mergeIntoTextdomain);
    }

    public function mergeTextdomainsTranslations(array $textdomains, $mergeIntoTextdomain = 'lang')
    {
        $storageService     = $this->getTranslationStorageService();
        $textdomains = array_diff($textdomains, array($mergeIntoTextdomain));
        
        foreach ($this->getLangService()->getLangsAvailable() as $lang) {
            foreach ($textdomains as $textdomain) {
                foreach ($storageService->getTranslations($textdomain, $lang) as $string => $translation) { 
                    $storageService->setTranslation($mergeIntoTextdomain, $lang, $string, $translation);
                }
            }
        }
        $storageService->persistFlushCache();
        return $this;
    }

    /**
     * Every string passed to __invoke is passed to the storage service
     * @return \GbiliLangModule\Service\TranslationStorage
     */
    public function getTranslationStorageService()
    {
        return $this->translationStorageService;
    }

    public function setTranslationStorageService($service)
    {
        $this->translationStorageService = $service;
        return $this;
    }

    public function getTextdomainService()
    {
        return $this->textdomainService;
    }

    public function setTextdomainService($service)
    {
        $this->textdomainService = $service;
        return $this;
    }

    public function getLangService()
    {
        return $this->langService;
    }

    public function setLangService($langService)
    {
        $this->langService = $langService;
        return $this;
    }
}
