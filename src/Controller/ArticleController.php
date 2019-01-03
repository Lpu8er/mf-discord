<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends BaseController
{
    /**
     * @Route("/", name="article_index", methods="GET")
     */
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $o = $request->query->has('p')? (1 - max(1, intval($request->query->get('p')))):null;
        if(empty($o)) { $o = null; }
        return $this->render('article/index.html.twig', ['articles' => $articleRepository->findBy([
            'status' => Article::STATUS_PUBLISHED,
        ], [
            'datePublished' => 'DESC',
        ], 20, $o)]); // @TODO soft code the limit
    }

    /**
     * @Route("/{slug}", name="article_show", methods="GET")
     */
    public function show($slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBySlug($slug);
        if(empty($article)) { throw $this->createNotFoundException(); } // @TODO less crude
        return $this->render('article/show.html.twig', ['article' => $article]);
    }
}
