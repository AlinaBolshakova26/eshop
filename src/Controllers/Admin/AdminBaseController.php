<?php

namespace Controllers\Admin;

use Controllers\BaseController;

abstract class AdminBaseController extends BaseController
{
    public function __construct()
    {
        $this->setLayout('layouts/admin_layout');

        if (!in_array($_SERVER['REQUEST_URI'], ['/admin/login', '/admin/login?error=1'])) 
        {
            $this->checkAdminAccess();
        }
    }

    protected function checkAdminAccess(): void
    {
        if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
            $this->redirect(url('admin.login'));
        }
    }
}
