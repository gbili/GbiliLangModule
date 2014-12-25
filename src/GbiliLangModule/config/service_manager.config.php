<?php
namespace GbiliLangModule;
return array(
    'factories' => array(
        'lang' => __NAMESPACE__ . '\Service\LangFactory',
        'textdomain' => function ($sm) {
            return new Service\Textdomain($sm);
        },
        'bulkTranslate'        => function ($sm) {
            $service = new Service\BulkTranslate;
            $service->setTextdomainService($sm->get('textdomain'));
            $service->setTranslationStorageService($sm->get('translationStorage'));
            return $service;
        },
        'translationMerger'        => function ($sm) {
            $service = new Service\TranslationMerger;
            $service->setTextdomainService($sm->get('textdomain'));
            $service->setTranslationStorageService($sm->get('translationStorage'));
            $service->setLangServic($sm->get('lang'));
            return $service;
        },
    ),

    'invokables' => array(
        'translationStorage' => __NAMESPACE__ . '\Service\TranslationStorage',
    ),

    'aliases' => array(
        'locale'             => 'lang',
    ),
);
