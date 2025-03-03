<?php 

function url($name, $params = []) 
{
    global $router;
    return $router->generateUrl($name, $params);
}

define('ROOT', dirname(__DIR__));

define('ITEMS_PER_PAGE', 9);

define("ITEMS_PER_PAGE_ADMIN", 30);

define("BY_RATING_OR_TAG_ITEMS_PER_PAGE_ADMIN", 10);
