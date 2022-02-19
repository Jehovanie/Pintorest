<?php

namespace App\Controller;

use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
