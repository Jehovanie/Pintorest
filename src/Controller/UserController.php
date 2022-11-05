<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user" , methods="GET")
     */
    public function index(): Response
    {
        return $this->render('user/profil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/new", name="new_user", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface  $entityManagerInterface): Response
    {
        $user = new User;

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", "User successful created...");

            return $this->redirectToRoute("app_home");
        }

        return $this->render('user/new.html.twig', [
            "formulaire" => $form->createView(),
            "user" => $user
        ]);
    }
}
