<?php

namespace App\Controller;

use App\Entity\Velo;
use App\Form\VeloType;
use App\Repository\VeloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/velo")
 */
class VeloController extends AbstractController
{
    /**
     * @Route("/", name="velo_index", methods={"GET"})
     */
    public function index(VeloRepository $veloRepository): Response
    {
        return $this->render('Velo/index.html.twig', [
            'velos' => $veloRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="velo_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Velo = new Velo();
        $form = $this->createForm(VeloType::class, $Velo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($Velo);
            $entityManager->flush();

            return $this->redirectToRoute('velo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Velo/new.html.twig', [
            'velo' => $Velo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="velo_show", methods={"GET"})
     */
    public function show(Velo $Velo): Response
    {
        return $this->render('Velo/show.html.twig', [
            'velo' => $Velo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="velo_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Velo $Velo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VeloType::class, $Velo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('velo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Velo/edit.html.twig', [
            'velo' => $Velo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="velo_delete", methods={"POST"})
     */
    public function delete(Request $request, Velo $Velo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Velo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($Velo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('velo_index', [], Response::HTTP_SEE_OTHER);
    }
}
