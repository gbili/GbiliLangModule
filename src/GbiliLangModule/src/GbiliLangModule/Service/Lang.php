<?php
namespace GbiliLangModule\Service;

class Lang
{
    protected $application = null;
    protected $defaultLang = 'en';

    public function __construct(\Zend\Mvc\Application $application)
    {
        $this->application = $application;
    }

    public function getLang()
    {
        $routeMatch = $this->application->getMvcEvent()->getRouteMatch();

        if (null === $routeMatch) {
            $config = $this->application->getServiceManager()->get('config');
            if (isset($config['lang']) && isset($config['lang']['default_lang'])) {
                return $config['lang']['default_lang'];
            }
            return $this->defaultLang;
        }

        $langParam = $routeMatch->getParam('lang');
        if (null === $langParam) {
            throw new \Exception('lang param not set yet');
        }
        return $langParam;
    }

    public function setDefault($default)
    {
        $this->defaultLang = $default;
        return $this;
    }

    public function getDefault()
    {
        return $this->defaultLang;
    }

    public function getAllLangs()
    {
        return $this->getLangsAvailable();
    }

    public function getLangsAvailable()
    {
        $config = $this->application->getServiceManager()->get('config');
        if (isset($config['lang']) && isset($config['lang']['langs_available'])) {
            return $config['lang']['langs_available'];
        }
        return array($this->getLang());
    }

    /**
     * Return the date time format for the current lang
     */
    public function getDateTimeFormat()
    {
        $dateTimeFormats = $this->getDateTimeFormats();
        if (isset($dateTimeFormats[$this->getLang()])) {
            return $dateTimeFormats[$this->getLang()];
        }
        return $dateTimeFormats[$this->getDefault()];
    }

    /**
     * Return the date time format for the current lang
     */
    public function getDateTimeFormats()
    {
        $config = $this->application->getServiceManager()->get('Config');
        $lang = $this->getLang();
        if (isset($config['lang']['date_time_formats'])) {
            return $config['lang']['date_time_formats'];
        }
        return array($this->getDefault() => \DateTime::ISO8601);
    }

    protected function getNamedGroupsRegexFromDateFormat($dateFormat)
    {
        $namedPatterns = array(
            'dd' => '(?P<dd>[0-9]{2})',
            'mm' => '(?P<mm>[0-9]{2})',
            'yy' => '(?P<yy>[0-9]{4})',
        );
        $patterns = array();
        $replacements = array();
        foreach ($namedPatterns as $name => $replacement) {
            $patterns[] = "/$name/";
            $replacements[] = $replacement;
        }
        $regexFormat = '#' . preg_replace($patterns, $replacements, $dateFormat) . '#';
        return $regexFormat;
    }

    protected function extractDatePartsFromFormat($dateValue, $format)
    {
        $regexFormat = $this->getNamedGroupsRegexFromDateFormat($format);
        if (!(0 < preg_match_all($regexFormat, $dateValue, $matches))) {
            return false;
        }
        return $matches;
    }

    protected function tryAllFormatsUntilDatePartsExtractionSucceedsOrFalse($dateValue, $skipCurrentLang=true)
    {
        $currentLang = $this->getLang();
        $dateParts = false;
        foreach ($this->getDateTimeFormats() as $lang => $format) {
            if ($skipCurrentLang && $lang === $currentLang) continue;
            $dateParts = $this->extractDatePartsFromFormat($dateValue, $format);
            if ($dateParts) break;
        }
        return $dateParts;
    }

    public function getStandardDate($dateValue)
    {
        $format = $this->getDateTimeFormat();
        $dateParts = $this->extractDatePartsFromFormat($dateValue, $format);
        if (!$dateParts) {
            $dateParts = $this->tryAllFormatsUntilDatePartsExtractionSucceedsOrFalse($dateValue, $skipCurrentLang=true);
        }

        if (!$dateParts) {
            return false;
        }

        $day = current($dateParts['dd']);
        $month = current($dateParts['mm']);
        $year = current($dateParts['yy']);

        $isoDate = "$year-$month-$day";
        return $isoDate;
    }

    public function getLocale()
    {
        throw new \Exception('use getLang() instead of getLocale()');
    }
}
