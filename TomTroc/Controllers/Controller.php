<?php

abstract class Controller
{   
    /**
     * Renders a view with the provided parameters.
     *
     * @param string $view The view file to render.
     * @param array $params The parameters to pass to the view.
     * @return void
     */
    protected function render(string $view, array $params = []): void
    {
        extract($params, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../Views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }
}
