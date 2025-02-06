<?php

return new PDO(
    "mysql:host=mysql-8.2;dbname=eshop",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);