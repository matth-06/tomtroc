<?php 
/**
 * Contrôleur de la partie admin.
 */
 
class AdminController {

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Affiche la page de monitoring admin.
     * @return void
     */
    public function showMonitoring() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // Récupération des paramètres de tri
        $sortColumn = Utils::request('sort', 'title'); // Par défaut tri par titre
        $sortOrder = Utils::request('order', 'asc'); // Par défaut ordre croissant

        // Validation des paramètres
        $validColumns = ['title', 'views', 'comments', 'date'];
        if (!in_array($sortColumn, $validColumns)) {
            $sortColumn = 'title';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // Pour chaque article, on récupère le nombre de commentaires.
        $commentManager = new CommentManager();
        $articlesWithStats = [];
        foreach ($articles as $article) {
            $comments = $commentManager->getAllCommentsByArticleId($article->getId());
            $articlesWithStats[] = [
                'article' => $article,
                'commentCount' => count($comments)
            ];
        }

        // Tri des articles selon les paramètres
        usort($articlesWithStats, function($a, $b) use ($sortColumn, $sortOrder) {
            $valueA = $this->getSortValue($a, $sortColumn);
            $valueB = $this->getSortValue($b, $sortColumn);

            if ($sortOrder === 'asc') {
                return $valueA <=> $valueB;
            } else {
                return $valueB <=> $valueA;
            }
        });

        // On affiche la page de monitoring.
        $view = new View("Monitoring Admin");
        $view->render("monitoring", [
            'articlesWithStats' => $articlesWithStats,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder
        ]);
    }

    /**
     * Fonction helper pour obtenir la valeur de tri selon la colonne.
     * @param array $item L'élément du tableau articlesWithStats
     * @param string $column La colonne de tri
     * @return mixed La valeur à utiliser pour le tri
     */
    private function getSortValue(array $item, string $column): mixed
    {
        switch ($column) {
            case 'title':
                return strtolower($item['article']->getTitle());
            case 'views':
                return $item['article']->getViewCount();
            case 'comments':
                return $item['commentCount'];
            case 'date':
                return $item['article']->getDateCreation()->getTimestamp();
            default:
                return 0;
        }
    }

    /**
     * Affiche la liste des commentaires pour un article en admin.
     * @return void
     */
    public function showArticleComments(): void
    {
        $this->checkIfUserIsConnected();

        $articleId = Utils::request('id', -1);
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($articleId);

        if (!$article) {
            throw new Exception('Article introuvable');
        }

        $commentManager = new CommentManager();
        $comments = $commentManager->getAllCommentsByArticleId($articleId);

        $view = new View('Commentaires article');
        $view->render('adminComments', [
            'article' => $article,
            'comments' => $comments
        ]);
    }

    /**
     * Supprime un commentaire en admin.
     * @return void
     */
    public function deleteComment(): void
    {
        $this->checkIfUserIsConnected();

        $commentId = Utils::request('id', -1);
        $commentManager = new CommentManager();
        $comment = $commentManager->getCommentById($commentId);

        if (!$comment) {
            throw new Exception('Commentaire introuvable');
        }

        $articleId = $comment->getIdArticle();
        $commentManager->deleteComment($comment);

        Utils::redirect('adminComments', ['id' => $articleId]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected() : void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm() : void 
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser() : void 
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser() : void 
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }
}