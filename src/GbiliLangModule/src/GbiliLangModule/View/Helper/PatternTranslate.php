<?php
namespace GbiliLangModule\View\Helper;

class PatternTranslate extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke($patterns, $replacements, $phrase, $textdomain = null)
    {
        if (null !== $textdomain) {
            throw new \Exception('Sorry, some changes were made to this helper signature, remove text domain; last param');
        }

        if (is_string($patterns)) {
            $patterns = array($pattenrs);
        }

        if (is_string($replacements)) {
            $replacements = array($replacements);
        }

        $view = $this->getView();

        $regexTranslatedPatterns = array_map(function ($pattern) use ($view){
            return '/' . $view->translate($pattern) . '/';
        }, $patterns);

        $translatedPhrase = $view->translate($phrase);

        return preg_replace($regexTranslatedPatterns, $replacements, $translatedPhrase);
    }
}
