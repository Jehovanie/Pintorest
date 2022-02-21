<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $repo): Response
    {
        $pins = $repo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/home.html.twig', ["pins" => $pins]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_show_pin")
     */
    public function show(PinRepository $repo, int $id): Response
    {
        $pin = $repo->find($id);

        return $this->render('pins/show.html.twig', compact("pin"));
    }

    /**
     * @Route("/pins/create", name="app_create_pin" , methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add("title", TextType::class)
            ->add("description", TextareaType::class)
            ->getform();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $pin = new Pin;

            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);

            $entityManager->persist($pin);
            $entityManager->flush();

            return $this->redirectToRoute("app_show_pin", ["id" => $pin->getId()]);
        }

        return $this->render('pins/new.html.twig', [
            "formulaire" => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/update/{id<[0-9]+>}", name="app_update_pin" , methods="GET|POST")
     */
    public function update(PinRepository $repo, Request $request, EntityManagerInterface $entityManager, int $id): Response
    {

        $pin = $repo->find($id);

        $form = $this->createFormBuilder($pin)
            ->add("title", TextType::class, ['attr' => ["value" => $pin->getTitle()]])
            ->add("description", TextareaType::class)
            ->getform();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $pin->setTitle($data->getTitle());
            $pin->setDescription($data->getDescription());

            $pin->updateTimestamp();

            $entityManager->persist($pin);
            $entityManager->flush();

            return $this->redirectToRoute("app_show_pin", ["id" => $pin->getId()]);
        }

        return $this->render('pins/update.html.twig', [
            "formulaire" => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/delete/{id<[0-9]+>}", name="app_delete_pin" , methods="GET|POST")
     */
    public function delete(PinRepository $repo, EntityManagerInterface $entityManager, int $id): Response
    {

        $pin = $repo->find($id);
        $entityManager->remove($pin);
        $entityManager->flush();

        // $pins = $repo->findBy([], ['createdAt' => 'DESC']);
        // return $this->render('pins/home.html.twig', ["pins" => $pins]);

        return $this->index($repo);
    }
}
