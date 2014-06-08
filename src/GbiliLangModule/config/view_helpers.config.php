<?php
namespace GbiliLangModule;
return array(
    'initializers' => array(
        'injectTranslatorTextDomain' => function ($helper, $vhp) {
            // TODO throws an exception texdomain is not explicitely set
            if (($helper instanceof \Zend\I18n\Translator\TranslatorAwareInterface)) {
                $textdomain = $vhp->getServiceLocator()->get('textdomain')->getTextdomain();
                $helper->setTranslatorTextDomain($textdomain);
            }
        }
    ),

    'invokables' => array(
        'patternTranslate' => __NAMESPACE__ . '\View\Helper\PatternTranslate',
        'renderSelectOptionalTranslation' => __NAMESPACE__ . '\View\Helper\FormSelect',
        'renderCustomizableOptionsRadio' => __NAMESPACE__ . '\View\Helper\FormRadio',
    ),

    'factories' => array(
        'langSelector' => __NAMESPACE__ . '\View\Helper\LangSelectorFactory',

        'lang' => function ($viewHelperPluginManager) {
            $currentLang = $viewHelperPluginManager->getServiceLocator()
                ->get('lang')->getLang();
            $langHelper = new View\Helper\Lang();
            $langHelper->setLang($currentLang);
            return $langHelper;
        },

        'dateTimeFormat' => function ($viewHelperPluginManager) {
            $service = $viewHelperPluginManager->getServiceLocator()
                ->get('lang');
            $helper = new View\Helper\DateTimeFormat();
            $helper->setService($service);
            return $helper;
        },
    ),
);
