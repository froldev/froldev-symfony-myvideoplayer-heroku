<?php

namespace App\Controller;

use App\Form\SearchVideoType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    const MAX_ITEMS_NAV = 4;
    const MAX_HOME_VIDEOS = 6;

    /**
     * @Route("/", name="home")
     */
    public function index(
        CategoryRepository $categoryRepository,
        VideoRepository $videoRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $form = $this->createForm(SearchVideoType::class);

        $videos = $paginator->paginate(
            $videoRepository->findBy(['is_best' => true]),
            $request->query->getInt('page', 1),
            self::MAX_HOME_VIDEOS
        );

        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'videos' => $videos,
            'formVideo' => $form->createView(),
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
            'max' => self::MAX_ITEMS_NAV,
        ]);
    }

    public function renderFooter(CategoryRepository $categoryRepository): Response
    {
        return $this->render('bricks/_footer.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
