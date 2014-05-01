<?php
namespace GbiliLangModule;
return array(
    'regex_patterns' => array(
        'lang'       => '(?:en)|(?:es)|(?:fr)|(?:de)|(?:it)',
        'id'         => '[0-9]+',
        'uniquename' => '[A-Za-z0-9](?:[-_.]?[A-Za-z0-9]){3,}',
        'dogname' => '[A-Za-z0-9](?: ?[A-Za-z0-9.-]){3,}', //No spaces should be allowed in route
        /*
         * No spaces should be allowed in route, they are replaced with _
         * underscores. Make sure to preg_replace('/ /', '_', $routeParam)
         */
        'dogname_underscored' => '[A-Za-z0-9](?:_?[A-Za-z0-9.-]){3,}', 
        'category' => '(?:(?:symptom)|(?:cause)|(?:solution))s?',
    ),
);
