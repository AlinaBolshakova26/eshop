<?php 

function url($name, $params = []) 
{
    global $router;
    return $router->generateUrl($name, $params);
}
