<?php
namespace GbiliLangModule\Form;

class BulkTranslate extends \Zend\Form\Form 
{
    public function __construct($name=null, array $options=array())
    {
        parent::__construct('bulk-translate', $options);

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Translate',
                'id' => 'submitbutton',
                'class' => 'btn btn-default', 
            ),
        ));
    }

    public function addTranslationElements(array $translationsParam, $textdomain=null, $locale=null)
    {
        if (null !== $locale) {
            $localeSpecificTranslations = $translationsParam;
            $this->add($this->getLocaleFieldset($localeSpecificTranslations, $locale));
            return;
        }

        if (null !== $textdomain) {
            $textdomainSpecificTranslations = $translationsParam;
            $this->add($this->getTextdomainFieldset($textdomainSpecificTranslations, $textdomain));
            return;
        }

        $allTextdomainAllLanguagesTranslation = $translationsParam;
        foreach ($allTextdomainAllLanguagesTranslation as $textdomain => $textdomainSpecificTranslations) {
            $this->add($this->getTextdomainFieldset($textdomainSpecificTranslations, $textdomain));
        }
    }

    public function getTextdomainFieldset($textdomainSpecificTranslations, $textdomain)
    {
        $textdomainFieldset = new \Zend\Form\Fieldset($textdomain);
        foreach ($textdomainSpecificTranslations as $locale => $translations) {
            $textdomainFieldset->add($this->getLocaleFieldset($translations));
        }
        return $textdomainFieldset;
    }

    public function getLocaleFieldset($translations, $locale)
    {
        $localeFieldset = new \Zend\Form\Fieldset($locale);
        $stringCount = 0;
        foreach ($translations as $string => $translation) {
            $fieldsetName = (string) $stringCount++;
            $translationFieldset = new \Zend\Form\Fieldset($fieldsetName);
            $translationFieldset->add(array(
                'type' => 'Zend\Form\Element\Hidden',
                'name' => 'string', 
                'attributes' => array(
                    'value' => $string,
                ),
            ));
            $translationFieldset->add(array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'translation', 
                'options' => array(
                    'label' => $string,
                ), 
                'attributes' => array(
                    'class' => 'form-control',
                    'value' => $translation,
                ),
            ));
            $localeFieldset->add($translationFieldset);
        }
        return $localeFieldset;
    }
}
