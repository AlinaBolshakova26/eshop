<?php

namespace Controllers;

class HomeController
{
    public static function index()
    {

        require_once __DIR__ . '/../Views/home/index.php';
    }
}