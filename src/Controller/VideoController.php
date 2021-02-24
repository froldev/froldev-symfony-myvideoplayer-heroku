<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Form\SearchVideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/video", name="video_")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(VideoRepository $videoRepository, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SearchVideoType::class);
        $form->handleRequest($request);

        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findBy([], [
                'name' => 'ASC',
            ]),
            'formVideo' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $video = new Video();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('video_index');
        }

        return $this->render('video/new.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Video $video, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('video_index');
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Video $video): Response
    {
        if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('video_index');
    }

    /**
     * @Route("/searchMovie/{search}", name="search_movie", methods={"GET", "POST"})
     */
    public function searchVideo(?String $search, VideoRepository $videoRepository): Response
    {
        if ($search == "all") {
            $videos = $videoRepository->findBy([], ['name' => 'ASC',]);
        } else {
            $videos = $videoRepository->findVideoBySearch($search);
        }

        $arrayVideos = [];
        foreach ($videos as $key => $value) {
            $arrayVideos[$key]['id'] = $value->getId();
            $arrayVideos[$key]['name'] = $value->getName();
            $arrayVideos[$key]['slug'] = $value->getSlug();
            $arrayVideos[$key]['author'] = $value->getAuthor();
            $arrayVideos[$key]['category'] = $value->getCategory()->getName();
        }

        return new JsonResponse([
            'videos' => $arrayVideos,
        ]);
    }
}
