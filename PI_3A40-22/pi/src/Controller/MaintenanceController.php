<?php

namespace App\Controller;

use App\Entity\Maintenance;
use App\Entity\Produit;
use App\Form\MaintenanceType;
use App\Repository\MaintenanceRepository;
use App\Repository\ProduitRepository;
use App\Repository\ReclamationRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/maintenance")
 */
class MaintenanceController extends Controller
{
    //supprimer
    /**
     * @Route("/deleteMain/{id}", name="deleteMan")
     */

    public function deleteMan(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $main = $em->getRepository(Maintenance::class)->find($id);
        if($main!=null ) {
            $em->remove($main);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("maintenance a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id maintenance invalide.");
    }
    //update
    /**
     * @Route("/modifierMain",name="modifierMain" , methods={"POST","GET"})
     */
    public function modifierMain(Request $request , MaintenanceRepository  $maintenanceRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $maintenance = new Maintenance();
        $maintenance = $maintenanceRepository->find($request->get("id"));

        $maintenance->setAdresse($request->get("adresse"));
        $maintenance->setDescription($request->get("description"));
        $maintenance->setEtat($request->get("etat"));
        $em->persist($maintenance);
        $em->flush();

        return new JsonResponse("maintenance a ete modifiee avec success.");

    }
    //display
    /**
     * @Route("/displayall",name="displayall", methods={"POST","GET"})
     */
    public function displayall(Request $request, NormalizerInterface $normalizer,SerializerInterface  $serializer ): JsonResponse
    {
        $main = $this->getDoctrine()->getManager()->getRepository(Maintenance::class)->findAll();
        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($produit);
        return  new JsonResponse($formatted);*/
        $jsonContent = $normalizer->normalize($main , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    //ajout mobile
    /**
     * @Route("/ajoutermaintenance",name="AjouterMaintenance")
     */
    public function AjouterMaintenance( NormalizerInterface $normalizer,Request $request , ReclamationRepository $reclamationRepository , ProduitRepository $produitRepository , UsersRepository  $usersRepository) :JsonResponse
    {
        $maintenance = new Maintenance();


        $maintenance->setDescription($request->get("description"));
        $maintenance->setAdresse($request->get("adresse"));
        $maintenance->setEtat($request->get("etat"));

        $maintenance->setDateDebut(new \DateTime());
        $date = new \DateTime();
        date_add($date, date_interval_create_from_date_string('30 days'));
        $maintenance->setDateFin($date);

        $user  = $usersRepository->find($request->get("idUser"));
        $maintenance->setRelation($user);
        $produit = $produitRepository->find($request->get("idProduit"));
        $maintenance->setIdProduit($produit);
        $reclamation = $reclamationRepository->find($request->get("reclamation"));
        $maintenance->setReclamation($reclamation);


        $em =$this->getDoctrine()->getManager();

        $em->persist($maintenance);
        $em->flush();


        $jsonContent = $normalizer->normalize($maintenance , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/maintenance_front", name="maintenance_index" , methods={"GET"})
     */
    public function hazem(MaintenanceRepository $maintenanceRepository): Response
    {
        return $this->render('/maintenance/maintenance_front.html.twig',[
            'maintenances' => $maintenanceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/front", name="maintenance_front" , methods={"GET"})
     */
    public function front(MaintenanceRepository $maintenanceRepository,PaginatorInterface $paginator, Request $request): Response
    {
        $maintenance = $maintenanceRepository->findAll();
       // $maintenance = $this->get('knp_paginator')->paginate(
        //    $maintenance,
         //   $request->query->getInt('page',1),
    //        8
    //    );

        return $this->render('/maintenance/front.html.twig',[
            'maintenances' => $maintenanceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="maintenance_index", methods={"GET"})
     */
    public function index(MaintenanceRepository $maintenanceRepository): Response
    {
        return $this->render('maintenance/index.html.twig', [
            'maintenances' => $maintenanceRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="maintenance_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maintenance = new Maintenance();
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($maintenance);
            $entityManager->flush();

            return $this->redirectToRoute('maintenance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maintenance/new.html.twig', [
            'maintenance' => $maintenance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="maintenance_show", methods={"GET"})
     */
    public function show(Maintenance $maintenance): Response
    {
        return $this->render('maintenance/show.html.twig', [
            'maintenance' => $maintenance,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="maintenance_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('maintenance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maintenance/edit.html.twig', [
            'maintenance' => $maintenance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="maintenance_delete", methods={"POST"})
     */
    public function delete(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maintenance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($maintenance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('maintenance_index', [], Response::HTTP_SEE_OTHER);
    }

}
