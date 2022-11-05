<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
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
     * @Route("/pins/new", name="app_create_pin" , methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, Pin $pin = null, UserRepository $userRepo): Response
    {
        if ($pin === null) {
            $pin = new Pin;
        }


        ///create the formulaire to add new pin.
        $form = $this->createForm(PinType::class, $pin);
        // $form = $this->createFormBuilder($pin)
        //     ->add("title", TextType::class)
        //     ->add("description", TextareaType::class)
        //     ->getform();
        dd($form);

        /* LORSQUE ON DEMANDE AVEC LE METHODE POST (envoie des informations via le formulaire) */
        $form->handleRequest($request);

        ///after the submitted form
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepo->findOneBy(["email" => "jehovanieram@gmail.com"]); ///default jhone doe
            ///our pin is related for one user.


            $pin->setUser($user);

            $entityManager->persist($pin);
            $entityManager->flush();

            $this->addFlash("success", "Pin succesfull created...");

            return $this->redirectToRoute("app_show_pin", ["id" => $pin->getId()]);
        }

        /* DEMANDE SYMPLEMENT DE LA PAGE QUI CONTIENT LA FORMULAIRE A REMPLIR */
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
        return $this->redirectToRoute("app_home");
    }


    /** 
     * @Route("/pins/{id<[0-9]+>}/update", name="app_update_pin" , methods={"GET" ,"PUT"})
     */
    public function update(Request $request, Pin $pin, EntityManagerInterface $entityManager): Response
    {

        ///create form and set their default value to $pin and return $pin with new value of pin.
        ///herite vient de la class AbstracController

        // $form = $this->createForm(PinType::class, $pin);
        $form = $this->createForm(PinType::class, $pin, ['method' => 'PUT']);
        ///envoie des informations via le requette
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($pin);
            $entityManager->flush();

            ///add message flash
            $this->addFlash("info", "Pin succesfull updated...");

            ///redirection
            return $this->redirectToRoute("app_show_pin", ["id" => $pin->getId()]);
        }

        ///rendre de la page formulaire pour l'action de modifier l'pin
        return $this->render('pins/update.html.twig', [
            "formulaire" => $form->createView(),
            "pin" => $pin
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_delete_pin" , methods={"POST" , "DELETE"})
     */
    public function delete(Pin $pin, EntityManagerInterface $entityManager): Response
    {
        ///provoque une message flash heriter vient la class AbstracController
        $this->addFlash("error", "Pin succesfull deleted...");

        ////entityManager responsable de l'action avec l'ORM
        $entityManager->remove($pin);
        $entityManager->flush();

        return $this->redirectToRoute("app_home");
    }
}
