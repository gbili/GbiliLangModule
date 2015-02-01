<?php
namespace GbiliLangModule;

return array(
    'lang' => array(
        'date_time_formats' =>array(
            'es' => 'dd/mm/yy',
            'fr' => 'dd-mm-yy',
            'en' => 'yy-mm-dd',
        ), 
        'langs_available' =>array(
            'es',
            'fr',
            'en',
            'de',
            'it',
        ), 
    ),

    'gbili_symlink_module' => array(
        'symlinks' => array(
            'gbili_lang_module_langnav_phtml' => __DIR__ . '/../view/partial/langnav.phtml'
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            strtolower(__NAMESPACE__) => __DIR__ . '/../view',
        ),
    ),

    'controllers'        => include __DIR__ . '/controllers.config.php',
    'controller_plugins' => include __DIR__ . '/controller_plugins.config.php',
    'navigation'         => include __DIR__ . '/navigation.config.php',
    'service_manager'    => include __DIR__ . '/service_manager.config.php',
    'translator'         => include __DIR__ . '/translator.config.php',
    'router'             => include __DIR__ . '/router.config.php',
    'view_helpers'       => include __DIR__ . '/view_helpers.config.php',
);
