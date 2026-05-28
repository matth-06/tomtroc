<?php

abstract class Controller
{
    protected function render(string $view, array $params = []): void
    {
        extract($params, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../Views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }
}
