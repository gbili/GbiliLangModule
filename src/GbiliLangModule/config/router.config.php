<?php
namespace GbiliLangModule;
return array(
    'routes' => array(
        'lang_translation_bulk_route' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '[/:lang]/lang/bulk/:textdomain',
                'constraints' => array(
                    'lang' => $preConfig['regex_patterns']['lang'],
                    'textdomain' =>'[a-z]+',
                ),
                'defaults' => array(
                    'lang'       => 'en',
                    'controller' => 'lang_translation_controller',
                    'textdomain' => strtolower(__NAMESPACE__),
                    'action'     => 'bulk',
                ),
            ),
            'may_terminate' => true,
        ),
        'lang_translation_merge_route' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '[/:lang]/lang/merge',
                'constraints' => array(
                    'lang' => $preConfig['regex_patterns']['lang'],
                ),
                'defaults' => array(
                    'lang'       => 'en',
                    'controller' => 'lang_translation_controller',
                    'action'     => 'merge',
                ),
            ),
            'may_terminate' => true,
        ),

        'lang_translation_index_route' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '[/:lang]/lang',
                'constraints' => array(
                    'lang' => $preConfig['regex_patterns']['lang'],
                    'textdomain' =>'[a-z]+',
                ),
                'defaults' => array(
                    'lang'       => 'en',
                    'controller' => 'lang_translation_controller',
                    'textdomain' => strtolower(__NAMESPACE__),
                    'action'     => 'index',
                ),
            ),
            'may_terminate' => true,
        ),
    ),
);
