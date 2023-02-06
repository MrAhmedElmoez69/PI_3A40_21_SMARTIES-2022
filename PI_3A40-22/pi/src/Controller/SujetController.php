<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Form\SujetFrontType;
use App\Form\SujetType;
use App\Repository\SujetRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/sujet")
 */
class SujetController extends Controller
{

    /**
     * @Route("/display",name="display_sujet" , methods={"POST","GET"})
     */
    public function display(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $Sujet = $this->getDoctrine()->getManager()->getRepository(Sujet::class)->findAll();
        $jsonContent = $normalizer->normalize($Sujet , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/display/{id}",name="display_single_sujet" , methods={"POST","GET"})
     */
    public function displaySingle(Request $request, NormalizerInterface $normalizer , $id): JsonResponse
    {
        $Sujet = $this->getDoctrine()->getManager()->getRepository(Sujet::class)->find($id);
        $jsonContent = $normalizer->normalize($Sujet , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/ajoutmobile",name="ajoutMobile" , methods={"POST","GET"})
     */
    public function ajoutMobile(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $sujet = new Sujet();
        $sujet->setTitre($request->get('titre'));
        $sujet->setContenu($request->get('contenu'));
        $sujet->setDate(new \DateTime());
        $user = $usersRepository->find($request->get('idUser'));
        $sujet->setIdUser($user);
        $sujet->setnbReponses(0);
        $sujet->setnbVues(0);
        $em->persist($sujet);
        $em->flush();
        $jsonContent = $normalizer->normalize($sujet , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/modifiermobile",name="modifierMobile" , methods={"POST","GET"})
     */
    public function modMobile(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $sujet = $this->getDoctrine()->getManager()
            ->getRepository(Sujet::class)
            ->find($request->get("id"));

        $titre = $request->query->get("titre");
        $contenu = $request->query->get("contenu");
        $idUser = $request->query->get("idUser");

        $sujet->setTitre($titre);
        $sujet->setContenu($contenu);
        $sujet->setIdUser($idUser);

        $em->persist($sujet);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($sujet);
        return new JsonResponse("Sujet a ete modifiee avec success.");

    }

    /**
     * @Route("/deletemobile/{id}",name="deleteMobile" , methods={"POST","GET"})
     */
    public function deleteMobile(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository , $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $sujet = $em->getRepository(Sujet::class)->find($id);
        $em->remove($sujet);
        $em->flush();
        $jsonContent = $normalizer->normalize($sujet , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/", name="sujet_index", methods={"GET"})
     */
    public function index(SujetRepository $sujetRepository): Response
    {
        return $this->render('sujet/index.html.twig', [
            'sujets' => $sujetRepository->findAll(),
        ]);
    }

    /**
     * @Route("/front", name="sujet_front", methods={"GET"})
     */
    public function indexfront(SujetRepository $sujetRepository,PaginatorInterface $paginator, Request $request): Response
    {
        if (null != $request->get('search')) {
            $sujets = $sujetRepository->findBy(['titre' => $request->get('search')]);
            $sujets = $this->get('knp_paginator')->paginate($sujets, $request->query->getInt('page', 1), 5);
            return $this->render('/sujet/sujettest.html.twig', [
                'sujets' => $sujets,
            ]);
        }

        $sujets = $sujetRepository->findAll();
        $sujets = $this->get('knp_paginator')->paginate($sujets,$request->query->getInt('page',1),5);
        return $this->render('sujet/sujettest.html.twig', [
            'sujets' => $sujets,
        ]);
    }

    /**
     * @Route("/frontdate", name="tridate", methods={"GET"})
     */
    public function indextridate(SujetRepository $sujetRepository,PaginatorInterface $paginator, Request $request): Response
    {
        if (null != $request->get('search')) {
            $sujets = $sujetRepository->findBy(['titre' => $request->get('search')]);
            $sujets = $this->get('knp_paginator')->paginate($sujets, $request->query->getInt('page', 1), 5);
            return $this->render('/sujet/sujettest.html.twig', [
                'sujets' => $sujets,
            ]);
        }
        $sujets = $sujetRepository->findBy([], ['Date' => 'ASC']);
        $sujets = $this->get('knp_paginator')->paginate($sujets,$request->query->getInt('page',1),5);
        return $this->render('sujet/sujettest.html.twig', [
            'sujets' => $sujets,
        ]);
    }

    /**
     * @Route("/frontvues", name="trivues", methods={"GET"})
     */
    public function indextrivues(SujetRepository $sujetRepository,PaginatorInterface $paginator, Request $request): Response
    {
        if (null != $request->get('search')) {
            $sujets = $sujetRepository->findBy(['titre' => $request->get('search')]);
            $sujets = $this->get('knp_paginator')->paginate($sujets, $request->query->getInt('page', 1), 5);
            return $this->render('/sujet/sujettest.html.twig', [
                'sujets' => $sujets,
            ]);
        }
        $sujets = $sujetRepository->findBy([], ['nbVues' => 'DESC']);
        $sujets = $this->get('knp_paginator')->paginate($sujets,$request->query->getInt('page',1),5);
        return $this->render('sujet/sujettest.html.twig', [
            'sujets' => $sujets,
        ]);
    }

    /**
     * @Route("/frontreponse", name="trireponse", methods={"GET"})
     */
    public function indextrireponse(SujetRepository $sujetRepository,PaginatorInterface $paginator, Request $request): Response
    {
        if (null != $request->get('search')) {
            $sujets = $sujetRepository->findBy(['titre' => $request->get('search')]);
            $sujets = $this->get('knp_paginator')->paginate($sujets, $request->query->getInt('page', 1), 5);
            return $this->render('/sujet/sujettest.html.twig', [
                'sujets' => $sujets,
            ]);
        }
        $sujets = $sujetRepository->findBy([], ['nbReponses' => 'DESC']);
        $sujets = $this->get('knp_paginator')->paginate($sujets,$request->query->getInt('page',1),5);
        return $this->render('sujet/sujettest.html.twig', [
            'sujets' => $sujets,
        ]);
    }

    /**
     * @Route("/new", name="sujet_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sujet = new Sujet();
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujet->setnbVues(0);
            $sujet->setnbReponses(0);

            $entityManager->persist($sujet);
            $entityManager->flush();

            return $this->redirectToRoute('sujet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sujet/new.html.twig', [
            'sujet' => $sujet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newfront", name="sujet_new_front", methods={"GET", "POST"})
     */
    public function newFront(Request $request, EntityManagerInterface $entityManager , UsersRepository $usersRepository): Response
    {
        $sujet = new Sujet();
        $form = $this->createForm(SujetFrontType::class, $sujet);
        $form->handleRequest($request);
        $sujet->setDate(new \DateTime());

        $user = $usersRepository->find($this->getUser()->getId());


        if ($form->isSubmitted() && $form->isValid()) {
            $sujet->setIdUser($user);
            $sujet->setnbReponses(0);
            $sujet->setnbVues(0);
            $entityManager->persist($sujet);
            $entityManager->flush();

            return $this->redirectToRoute('sujet_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sujet/SujetCreateFront.html.twig', [
            'sujet' => $sujet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sujet_show", methods={"GET"})
     */
    public function show(Sujet $sujet): Response
    {
        return $this->render('sujet/show.html.twig', [
            'sujet' => $sujet,
        ]);
    }

    /**
     * @Route("/front/{id}", name="sujet_show_front", methods={"GET"})
     */
    public function showFront(Sujet $sujet,EntityManagerInterface $entityManager): Response
    {
        $nb = $sujet->getnbVues();
        $nb = $nb + 1;
        $sujet->setnbVues($nb);
        $entityManager->persist($sujet);
        $entityManager->flush();
        return $this->render('sujet/SujetShowOne.html.twig', [
            'sujet' => $sujet,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sujet_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sujet $sujet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('sujet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sujet/edit.html.twig', [
            'sujet' => $sujet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit/front", name="sujet_front_edit", methods={"GET", "POST"})
     */
    public function editFront(Request $request, Sujet $sujet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SujetFrontType::class, $sujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('sujet_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sujet/edit_front.html.twig', [
            'sujet' => $sujet,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="sujet_delete", methods={"POST"})
     */
    public function delete(Request $request, Sujet $sujet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sujet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sujet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sujet_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete/{id}", name="sujet_delete_front", methods={"POST"})
     */
    public function Front(Request $request, Sujet $sujet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sujet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sujet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sujet_front', [], Response::HTTP_SEE_OTHER);
    }


}

