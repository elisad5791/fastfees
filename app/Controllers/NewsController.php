<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use Slim\Views\Twig;

class NewsController
{   
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    } 

    public function index(Request $request, Response $response, $args) 
    {
        $queryParams = $request->getQueryParams();
        $page = (int) ($queryParams['page'] ?? 1);
        $offset = 10 * ($page - 1);

        $sql = "SELECT * FROM news ORDER BY created_at DESC LIMIT 10 OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $news = $stmt->fetchAll();

        $sql = "SELECT count(*) AS count FROM news";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch();
        $count = $res['count'];

        $pages = ceil($count / 10);

        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'news.html.twig', [
            'news' => $news,
            'pages' => $pages
        ]);
    }
}