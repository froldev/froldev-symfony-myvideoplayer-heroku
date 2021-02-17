<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    public function renderNavBar(CategoryRepository $categoryRepository): Response
    {
        return $this->render('bricks/_navbar.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    public function renderFooter(CategoryRepository $categoryRepository): Response
    {
        return $this->render('bricks/_footer.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
