<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\SearchCategoryType;
use App\Form\SearchVideoType;
use App\Repository\VideoRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    const MAX_CATEGORY_VIDEOS = 9;

    /**
     * @Route("/", name="index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(SearchCategoryType::class);

        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findBy([], [
                'position' => 'ASC',
            ]),
            'formCategory' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * nom avec 1ere lettre en majuscule
     * position en automatique
     */
    public function new(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
    ): Response {
        $category = new Category();
        $form = $this
            ->createForm(CategoryType::class, $category)
            ->remove('position');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName(ucfirst(strtolower($category->getName())));
            $category->setPosition($categoryRepository->countCategories() + 1);

            $em->persist($category);
            $this->addFlash("success", "La catégorie " . $category->getName() . " a bien été ajoutée !");
            $em->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'position' => false,
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(
        Category $category,
        Request $request,
        VideoRepository $videoRepository,
        PaginatorInterface $paginator
    ): Response {

        $videos = $paginator->paginate(
            $category->getVideos(),
            $request->query->getInt('page', 1),
            self::MAX_CATEGORY_VIDEOS
        );

        $form = $this->createForm(SearchVideoType::class);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'videos' => $videos,
            'formVideo' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * nom avec 1ere lettre en majuscule
     */
    public function edit(Request $request, EntityManagerInterface $em, Category $category, CategoryRepository $categoryRepository): Response
    {
        $position = $category->getPosition();

        $arrayPositions = [];
        $categories = $categoryRepository->findAll();
        for ($i = 1; $i <= count($categories); $i++) {
            $arrayPositions[$i] = $i;
        }

        $form = $this->createForm(CategoryType::class, $category, [
            'positions' => array_flip($arrayPositions),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $otherCategory = $categoryRepository->findOneBy($categoryRepository->getIdByPosition($form->getData()->getPosition()));
            $otherCategory->setPosition($position);

            $category->setName(ucfirst(strtolower($category->getName())));
            $em->flush();

            $this->addFlash("success", "La catégorie " . $category->getName() . " a bien été modifiée !");
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'position' => true,
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="delete", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(
        Category $category,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
    ): Response {

        $em->remove($category);
        $em->flush();

        $categories = $categoryRepository->findBy([], ['position' => 'ASC',]);

        $arrayCategories = [];
        foreach ($categories as $key => $value) {
            $arrayCategories[$key]['name'] = $value->getName();
            $arrayCategories[$key]['position'] = $value->getPosition();
            $arrayCategories[$key]['slug'] = $value->getSlug();
        }

        return new JsonResponse([
            'categories' => $arrayCategories,
        ]);
    }

    /**
     * @Route("/searchCategory/{search}", name="search_movie", methods={"GET", "POST"})
     */
    public function searchVideo(?String $search, CategoryRepository $categoryRepository): Response
    {
        if ($search == "all") {
            $categories = $categoryRepository->findBy([], ['position' => 'ASC',]);
        } else {
            $categories = $categoryRepository->findCategoryBySearch($search);
        }

        $arrayCategories = [];
        foreach ($categories as $key => $value) {
            $arrayCategories[$key]['name'] = $value->getName();
            $arrayCategories[$key]['position'] = $value->getPosition();
            $arrayCategories[$key]['slug'] = $value->getSlug();
        }

        return new JsonResponse([
            'categories' => $arrayCategories,
        ]);
    }

    private function getArrayPositions(CategoryRepository $categoryRepository): array
    {
        $arrayPositions = [];
        $positions = $categoryRepository->getIdAndPositions();
        foreach ($positions as $key => $value) {
            $arrayPositions[$key] = $value['position'];
        }
        return $arrayPositions;
    }

    /**
     * @Route("/{slug}/searchMovieByCategory/{search}", name="search_movie_by_category", methods={"GET", "POST"})
     */
    public function searchVideoByCategory(
        Category $category,
        ?String $search,
        CategoryRepository $categoryRepository,
        VideoRepository $videoRepository
    ): Response {
        if ($search == "all") {
            $videos = $categoryRepository->findBy([], ['position' => 'ASC',]);
        } else {
            $videos = $categoryRepository->findVideoByCategoryAndSearch($category, $search);
        }

        $arrayVideos = [];
        foreach ($videos as $key => $value) {
            $arrayVideos[$key]['name'] = $value['name'];
            $arrayVideos[$key]['slug'] = $value['slug'];
            $arrayVideos[$key]['url'] = $value['url'];
        }

        return new JsonResponse([
            'videos' => $arrayVideos,
        ]);
    }
}
