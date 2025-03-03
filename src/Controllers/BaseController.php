<?php

namespace Controllers;

use Core\View;

abstract class BaseController
{
    protected string $layout = 'layouts/main_template';

    protected function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    protected function render(string $template, array $data = [], array $layoutData = []): void
    {
        $content = View::make($template, $data);

        $layoutParams = array_merge(
            ['content' => $content],
            $layoutData
        );

        echo View::make($this->layout, $layoutParams);
    }

    protected function renderWithoutLayout(string $template, array $data = []): void
    {
        $content = View::make($template, $data);
        echo $content;
    }


    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function checkLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(url('user.login'));
        }
    }
}
