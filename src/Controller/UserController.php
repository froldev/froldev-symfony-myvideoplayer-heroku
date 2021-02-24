<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\SearchUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepository): Response
    {
        $form = $this->createForm(SearchUserType::class);

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findBy([], [
                'username' => 'ASC',
            ]),
            'urlAdmin' => User::URL_ADMIN,
            'formUser' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "L\'utilisateur " . $user->getUsername() . " a bien été ajouté !");
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'urlAdmin' => User::URL_ADMIN,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        User $user
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getEmail() === User::URL_ADMIN) {
                $user->setEmail(User::URL_ADMIN);
            }
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->flush();

            $this->addFlash("success", "L\'utilisateur " . $user->getUsername() . " a bien été modifié !");
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'urlAdmin' => User::URL_ADMIN,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($user->getEmail() !== User::URL_ADMIN) {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }
        $this->addFlash("success", "L'utilisateur " . $user->getUsername() . " a bien été supprimé !");
        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/searchUser/{search}", name="search_user", methods={"GET", "POST"})
     */
    public function searchVideo(?String $search, UserRepository $userRepository): Response
    {
        if ($search == "all") {
            $users = $userRepository->findBy([], ['username' => 'ASC',]);
        } else {
            $users = $userRepository->findUserBySearch($search);
        }

        $arrayUsers = [];
        foreach ($users as $key => $value) {
            $arrayUsers[$key]['id'] = $value->getId();
            $arrayUsers[$key]['username'] = $value->getUsername();
            $arrayUsers[$key]['email'] = $value->getEmail();
            if ($value->getEmail()  !== User::URL_ADMIN) {
                $arrayUsers[$key]['delete'] = true;
            } else {
                $arrayUsers[$key]['delete'] = false;
            }
        }

        return new JsonResponse([
            'users' => $arrayUsers,
        ]);
    }
}
