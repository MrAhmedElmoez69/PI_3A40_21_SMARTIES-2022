<?php

namespace App\Controller;

use App\Entity\Emplacement;
use App\Form\EmplacementType;
use App\Repository\AccessoireRepository;
use App\Repository\EmplacementRepository;
use App\Repository\ProduitRepository;
use App\Repository\VeloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;

/**
 * @Route("/emplacement")
 */
class EmplacementController extends AbstractController
{


    /**
     * @Route("/", name="emplacement_index", methods={"GET"})
     */
    public function index(EmplacementRepository $emplacementRepository): Response
    {
        return $this->render('emplacement/index.html.twig', [
            'emplacements' => $emplacementRepository->findAll(),
        ]);
    }
    /**
     * @Route("/siteF", name="mariem_f", methods={"GET"})
     */
    public function mariem_f(EmplacementRepository $emplacementRepository): Response
    {
        return $this->render('/emplacement/site_front.html.twig', [
            'emplacements' => $emplacementRepository->findAll(),
        ]);
    }
    /**
     * @Route("/explore_site/{id}", name="explore1" , methods={"GET"})
     */
    public function explore1(EmplacementRepository $emplacementRepository,$id): Response
    {
        $emplacement = $emplacementRepository->find($id);
        return $this->render('/emplacement/ExploreSite.html.twig', [
            'emplacement' => $emplacement,
        ]);
    }

    /**
     * @Route("/new", name="emplacement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $emplacement = new Emplacement();
        $form = $this->createForm(EmplacementType::class, $emplacement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($emplacement);
            $entityManager->flush();

            return $this->redirectToRoute('emplacement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emplacement/new.html.twig', [
            'emplacement' => $emplacement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="emplacement_show", methods={"GET"})
     */
    public function show(Emplacement $emplacement): Response
    {
        return $this->render('emplacement/show.html.twig', [
            'emplacement' => $emplacement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="emplacement_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Emplacement $emplacement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmplacementType::class, $emplacement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('emplacement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emplacement/edit.html.twig', [
            'emplacement' => $emplacement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="emplacement_delete", methods={"POST"})
     */
    public function delete(Request $request, Emplacement $emplacement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emplacement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($emplacement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('emplacement_index', [], Response::HTTP_SEE_OTHER);
    }
}
