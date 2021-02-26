<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\SearchCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
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
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * nom avec 1ere lettre en majuscule
     */
    public function edit(Request $request, EntityManagerInterface $em, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
}
