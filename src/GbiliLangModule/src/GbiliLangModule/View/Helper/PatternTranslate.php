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

        return preg_replace(
            $this->getTranslatedPatternsBetweenRegexDelimiters($patterns),
            $this->getReplacementsAsStrings($replacements),
            $this->getView()->translate($phrase)
        );
    }

    /**
     * preg_replace needs patterns into regex delimiters
     * @note this makes the bad assumption that patterns do not contain a slash
     * @return array of patterns between /patterns/
     */
    protected function getTranslatedPatternsBetweenRegexDelimiters($patterns)
    {
        $view = $this->getView();
        return array_map(function ($pattern) use ($view){
            return '/' . $view->translate($pattern) . '/';
        }, $patterns);
    }

    /*
     * Replacements can be callable functions which take the view as first parameter
     * This method will call the callables which should return a string
     * @return array of replacements as strings
     */
    protected function getReplacementsAsStrings($replacements)
    {
        $view = $this->getView();
        return array_map(function ($replacement) use ($view){
            return (is_callable($replacement))
                ? call_user_func($replacement, $view)
                : $replacement;
        }, $replacements);
    }
}
