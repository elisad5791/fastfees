<?php

namespace App\Controllers;

use App\Services\NewsService;
use App\Session\SessionHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class SearchController
{   
    public function __construct(
        protected SessionHelper $sessionHelper,
        protected NewsService $newsService
    ) {}

    public function index(Request $request, Response $response, $args): Response
    {
        $queryParams = $request->getQueryParams();
        $q = htmlspecialchars($queryParams['q'] ?? '');
        
        $username = $this->sessionHelper->getUsername();
        $isAdmin = $this->sessionHelper->getIsAdmin();
        $news = $this->newsService->getSearchNews($q);
        
        $view = Twig::fromRequest($request);

        return $view->render($response, 'search.html.twig', [
            'is_admin' => $isAdmin,
            'username' => $username,
            'news' => $news,
            'q' => $q,
        ]);
    }
}