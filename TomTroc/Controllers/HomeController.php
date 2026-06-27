<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Book.php';

class HomeController extends Controller
{   
    /**
     * Displays the home page with the latest books.
     *
     * @return void
     */
    public function index(): void
    {
        $title = 'Accueil';
        $latestBooks = Book::getLatest(4);
        $this->render('home.php', compact('title', 'latestBooks'));
    }

    /**
     * Renders a page with the provided view and parameters.
     *
     * @param string $view The view file to render.
     * @param array $params The parameters to pass to the view.
     * @return void
     */
    public function renderPage(string $view, array $params = []): void
    {
        $this->render($view, $params);
    }
}
