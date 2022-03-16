<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home" , methods="GET")
     */
    public function index(PinRepository $repo): Response
    {
        $pins = $repo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/home.html.twig', ["pins" => $pins]);
    }

    /**
     * @Route("/pins/create", name="app_create_pin" , methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, Pin $pin = null): Response
    {
        if ($pin === null) {
            $pin = new Pin;
        }

        $form = $this->createForm(PinType::class, $pin);
        // $form = $this->createFormBuilder($pin)
        //     ->add("title", TextType::class)
        //     ->add("description", TextareaType::class)
        //     ->getform();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($pin);
            $entityManager->flush();

            $this->addFlash("success", "Pin succesfull created...");

            return $this->redirectToRoute("app_show_pin", ["id" => $pin->getId()]);
        }

        return $this->render('pins/new.html.twig', [
            "formulaire" => $form->createView(),
            "pin" => $pin
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_show_pin" , methods="GET")
     */
    public function show(Pin $pin): Response
    {

        if ($pin) {
            return $this->render('pins/show.html.twig', compact("pin"));
        }
    }


    /**
     * @Route("/pins/{id<[0-9]+>}/update", name="app_update_pin" , methods="GET|POST")
     */
    public function update(Request $request, Pin $pin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($pin);
            $entityManager->flush();

            $this->addFlash("success", "Pin succesfull updated...");

            return $this->redirectToRoute("app_show_pin", ["id" => $pin->getId()]);
        }

        return $this->render('pins/update.html.twig', [
            "formulaire" => $form->createView(),
            "pin" => $pin
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_delete_pin" , methods="POST|DELETE")
     */
    public function delete(Pin $pin, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash("error", "Pin succesfull deleted...");

        $entityManager->remove($pin);

        $entityManager->flush();

        return $this->redirectToRoute("app_home");
    }
}
