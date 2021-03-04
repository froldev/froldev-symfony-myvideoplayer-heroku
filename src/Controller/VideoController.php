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
    public function index(
        VideoRepository $videoRepository,
        Request $request
    ): Response {

        $form = $this->createForm(SearchVideoType::class);

        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findBy([], ['title' => 'ASC',]),
            'formVideo' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * 1ere lettre du titre de la video en masjuscule
     * slug en minuscule
     * 1ere lettre des mots de l'auteur en majuscule
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $video = new Video();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setTitle(ucfirst(strtolower($video->getTitle())));
            $video->setAuthor(ucwords(strtolower($video->getAuthor())));
            $em->persist($video);
            $em->flush();

            $this->addFlash("success", "La vidéo a bien été ajoutée !");
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
     * 1ere lettre du titre de la video en masjuscule
     * slug en minuscule
     * 1ere lettre des mots de l'auteur en majuscule
     */
    public function edit(Request $request, Video $video, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setTitle(ucfirst(strtolower($video->getTitle())));
            $video->setAuthor(ucwords(strtolower($video->getAuthor())));
            $em->flush();
            $this->addFlash("success", "La vidéo a bien été modifiée !");
            return $this->redirectToRoute('video_index');
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="delete", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(
        Video $video,
        EntityManagerInterface $em,
        VideoRepository $videoRepository
    ): Response {

        $em->remove($video);
        $em->flush();

        $videos = $videoRepository->findBy([], ['title' => 'ASC',]);

        $arrayVideos = [];
        foreach ($videos as $key => $value) {
            $arrayVideos[$key]['id'] = $value->getId();
            $arrayVideos[$key]['title'] = $value->getTitle();
            $arrayVideos[$key]['slug'] = $value->getSlug();
            $arrayVideos[$key]['author'] = $value->getAuthor();
            $arrayVideos[$key]['category'] = $value->getCategory()->getName();
        }

        return new JsonResponse([
            'videos' => $arrayVideos,
        ]);
    }

    /**
     * @Route("/searchMovie/{search}", name="search_movie", methods={"GET", "POST"})
     */
    public function searchVideo(?String $search, VideoRepository $videoRepository): Response
    {
        if ($search == "all") {
            $videos = $videoRepository->findBy([], ['title' => 'ASC',]);
        } else {
            $videos = $videoRepository->findVideoBySearch($search);
        }

        $arrayVideos = [];
        foreach ($videos as $key => $value) {
            $arrayVideos[$key]['id'] = $value->getId();
            $arrayVideos[$key]['title'] = $value->getTitle();
            $arrayVideos[$key]['slug'] = $value->getSlug();
            $arrayVideos[$key]['url'] = $value->getUrl();
            $arrayVideos[$key]['author'] = $value->getAuthor();
            $arrayVideos[$key]['category'] = $value->getCategory()->getName();
        }

        return new JsonResponse([
            'videos' => $arrayVideos,
        ]);
    }
}
