<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/homepage", name="home_admin")
     */
    public function indexAdmin(
        CategoryRepository $categoryRepository,
        VideoRepository $videoRepository,
        UserRepository $userRepository
    ): Response {
        return $this->render('home/indexAdmin.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'videos' => $videoRepository->findAll(),
            'users' => $userRepository->findAll(),
            'maxLink' => 4,
        ]);
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
