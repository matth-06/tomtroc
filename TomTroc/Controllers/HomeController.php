<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Book.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $title = 'Accueil';
        $latestBooks = Book::getLatest(4);
        $this->render('home.php', compact('title', 'latestBooks'));
    }

    public function renderPage(string $view, array $params = []): void
    {
        $this->render($view, $params);
    }
}
