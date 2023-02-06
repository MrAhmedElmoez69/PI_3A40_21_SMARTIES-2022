<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Favoris;
use App\Entity\Produit;
use App\Form\CommandeFrontType;
use App\Form\FavorisType;
use App\Repository\FavorisRepository;
use App\Repository\ProduitRepository;
use App\Repository\UsersRepository;
use App\Repository\VeloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;

/**
 * @Route("/favoris")
 */
class FavorisController extends Controller
{
    /**
     * @Route("/", name="favoris_index", methods={"GET"})
     */
    public function index(FavorisRepository $favorisRepository): Response
    {
        return $this->render('favoris/index.html.twig', [
            'favoris' => $favorisRepository->findAll(),
        ]);
    }
    /**
     * @Route("/favori1", name="favoris1", methods={"GET"})
     */
    public function front(FavorisRepository $favorisRepository,Request $request): Response
    {
        $test = $favorisRepository->findAll();

        $test = $this->get('knp_paginator')->paginate(
            $test,
            $request->query->getInt('page',1),3
        );
        if (null != $request->get('search')) {
            $test = $this->getDoctrine()->getRepository(Favoris::class)->findBy(['id' => $request->get('search')]);
            $test = $this->get('knp_paginator')->paginate($test, $request->query->getInt('page',1), 3);
            return $this->render('/produit/favoris.html.twig', [
                'Produits' => $test,
                'flash'=> $request->get('flash'),
            ]);
        }
        $flash = 1;
        return $this->render('produit/favoris.html.twig', [
            'Produits' => $test,
            'flash'=> $request->get('flash'),
        ]);
    }


    /**
     * @Route("/new", name="favoris_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $favori = new Favoris();
        $form = $this->createForm(FavorisType::class, $favori);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($favori);
            $entityManager->flush();

            return $this->redirectToRoute('favoris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('favoris/new.html.twig', [
            'favori' => $favori,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="favoris_show", methods={"GET"})
     */
    public function show(Favoris $favori): Response
    {
        return $this->render('favoris/show.html.twig', [
            'favori' => $favori,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="favoris_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Favoris $favori, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FavorisType::class, $favori);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('favoris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('favoris/edit.html.twig', [
            'favori' => $favori,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="favoris_delete", methods={"POST"})
     */
    public function delete(Request $request, Favoris $favori, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$favori->getId(), $request->request->get('_token'))) {
            $entityManager->remove($favori);
            $entityManager->flush();
        }

        return $this->redirectToRoute('favoris1', [], Response::HTTP_SEE_OTHER);
    }

}
