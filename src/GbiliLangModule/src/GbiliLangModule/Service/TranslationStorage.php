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
 * This writes translations as an array to module/<modulename>/language/<locale>.php
 */
class TranslationStorage
{
    /**
     * filename => array(string=>translation, ...)
     */
    protected $cache = array();
    protected $modulesDir;

    /**
     * @var \Zend\Filter\DashToCamelCase
     */
    protected $moduleInflector;

    public function __construct()
    {
        $this->moduleInflector = new \Zend\Filter\DashToCamelCase();
        $appDir = realpath(__DIR__ . str_repeat('/..', 8));
        $this->setModulesDir($appDir . '/module');
    }

    public function isTranslated($textdomain, $locale, $string)
    {
        $translations = $this->getCache($textdomain, $locale);
        return $this->isStringTranslated($translations, $string);
    } 

    public function isStringTranslated($translations, $string)
    {
        return isset($translations[$string]) && '' !== $translations[$string];
    }

    public function getTranslation($textdomain, $locale, $string)
    {
        if (!$this->isTranslated($textdomain, $locale, $string)) {
            throw new \Exception("There is no translation for textdomain:$textdomain, locale:$locale, string:$string");
        }
        $translations = $this->getTranslations($textdomain, $locale);
        return $translations[$string];
    }

    public function getTranslations($textdomain, $locale, $returnOnlyTranslated=false)
    {
        $translations = $this->getCache($textdomain, $locale);
        if (!$returnOnlyTranslated) {
            return $translations;
        }

        $onlyTranslated = array();
        foreach ($translations as $string => $translation) {
            if ('' === $translation) continue;
            $onlyTranslated[$string] = $translation;
        }
        return $onlyTranslated;
    }

    public function setModulesDir($dir)
    {
        if (!is_dir($dir)) {
            throw new \Exception('Directory does not exist: ' . $dir);
        }
        $this->modulesDir = $dir;
        return $this;
    }

    public function getModulesDir()
    {
        return $this->modulesDir;
    }

    public function getLocalizedFilename($dashModuleName, $locale)
    {
        $moduleName = $this->moduleInflector->filter($dashModuleName);
        $moduleDir = realpath($this->getModulesDir() .  '/' . $moduleName);
        if (false === $moduleDir) {
            throw new \Exception('Module dir does not exist. Dash Module name is: ' . $dashModuleName . '. Camel Case Module Name: ' . $moduleName);
        }
        $languageDir = realpath($moduleDir . '/language');
        if (false === $languageDir) {
            throw new \Exception("Module $moduleName has no ./language dir");
        }
        return $languageDir . "/$locale.php";
    }
    
    public function getCache($textdomain, $locale)
    {
        if (isset($this->cache[$textdomain][$locale])) {
            return $this->cache[$textdomain][$locale];
        }

        $filename = $this->getLocalizedFilename($textdomain, $locale);

        if (!file_exists($filename)) {
            $contents =  array();
        } else {
            $contents = include $filename;
        }

        $isFileEmpty = (1 === $contents);
        if ($isFileEmpty) {
            $contents = array();
        }

        if (!isset($this->cache[$textdomain])) {
            $this->cache[$textdomain] = array();
        }

        $this->cache[$textdomain][$locale] = $contents;
        return $contents;
    }

    public function setTranslation($textdomain, $locale, $string, $translation='', $overwrite=false)
    {
        if ($overwrite || !$this->isTranslated($textdomain, $locale, $string)) {
            $this->cache[$textdomain][$locale][$string] = $translation;
        }
    }

    public function __destruct()
    {
        $this->persistFlushCache();
    }

    public function persistFlushCache()
    {
        $cache = $this->cache;
        foreach ($cache as $textdomain => $locales) {
            foreach ($locales as $locale => $cache) {
                $this->persist($textdomain, $locale, $cache);
                unset($this->cache[$textdomain][$locale]);
            }
            unset($this->cache[$textdomain]);
        }
    }

    protected function persist($textdomain, $locale, $cache)
    {
        if (empty($cache)) {
            return;
        }

        $filename = $this->getLocalizedFilename($textdomain, $locale);

        if (!is_writable(dirname($filename))) {
            throw new \Exception('Cannot write in : ' . $filename);
        }

        $contents = "<?php\nreturn " . var_export($cache, true) . ";";
        return file_put_contents($filename, $contents);
    }
}
