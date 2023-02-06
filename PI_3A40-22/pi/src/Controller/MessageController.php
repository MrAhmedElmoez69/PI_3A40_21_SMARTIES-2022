<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Sujet;
use App\Form\MessageFrontType;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\SujetRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{

    /**
         * @Route("/display/{id}",name="display_message" , methods={"POST","GET"})
     */
    public function display(Request $request, NormalizerInterface $normalizer , int $id): JsonResponse
    {
        $sujet = $this->getDoctrine()->getManager()->getRepository(Sujet::class)->find($id);
        $Message = $this->getDoctrine()->getManager()->getRepository(Message::class)->findBy(['idSujet' => $sujet]);
        $jsonContent = $normalizer->normalize($Message , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/displaysingle/{id}",name="display_single_message" , methods={"POST","GET"})
     */
    public function displaySingle(Request $request, NormalizerInterface $normalizer , $id): JsonResponse
    {
        $Sujet = $this->getDoctrine()->getManager()->getRepository(Message::class)->find($id);
        $jsonContent = $normalizer->normalize($Sujet , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /**
     * @Route("/affichage",name="affichage" , methods={"POST","GET"})
     */
    public function affichage(Request $request, NormalizerInterface $normalizer ): JsonResponse
    {
        //$sujet = $this->getDoctrine()->getManager()->getRepository(Sujet::class);
        $Message = $this->getDoctrine()->getManager()->getRepository(Message::class)->findAll();
        $jsonContent = $normalizer->normalize($Message , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /**
     * @Route("/ajoutmobile",name="ajout_mobile_message" , methods={"POST","GET"})
     */
    public function ajoutMobile(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository, SujetRepository $sujetRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $message = new Message();
        $user = $usersRepository->find($request->get('idUser'));
        $message->setIdUser($user);
        $message->setDate(new \DateTime());
        $sujet = $sujetRepository->find($request->get('idSujet'));
        $message->setIdSujet($sujet);
        $message->setContenu($request->get('contenu'));
        $em->persist($message);
        $em->flush();
        $jsonContent = $normalizer->normalize($message , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/modifiermobile",name="modifierMobileMessage" , methods={"POST","GET"})
     */
    public function modMobile(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $message = $this->getDoctrine()->getManager()
            ->getRepository(Message::class)
            ->find($request->get("id"));

        $contenu = $request->get("contenu");

        $message->setContenu($contenu);

        $em->persist($message);
        $em->flush();

        return new JsonResponse("message a ete modifiee avec success.");

    }

    /**
     * @Route("/deletemobile/{id}",name="deleteMobileMessage" , methods={"POST","GET"})
     */
    public function deleteMobile(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository , $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository(Message::class)->find($id); 
        $em->remove($message);
        $em->flush();
        $jsonContent = $normalizer->normalize($message , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }


    /**
     * @Route("/", name="message_index", methods={"GET"})
     */
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="message_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newfront/{id}", name="message_new_front", methods={"GET", "POST"})
     */
    public function newFront(Request $request, EntityManagerInterface $entityManager , int $id , SujetRepository $sujetRepository, UsersRepository $usersRepository): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageFrontType::class, $message);
        $form->handleRequest($request);
        $user = $usersRepository->find($this->getUser()->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $test = $sujetRepository->find($id);
            $test->setnbReponses($test->getnbReponses()+1);
            $entityManager->persist($test);
            $message->setIdSujet($test);
            $message->setDate(new \DateTime());
            $message->setIdUser($user);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('sujet_show_front', array(  'id'=> $test->getId()), Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/msgNewFront.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_show", methods={"GET"})
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit/front", name="message_front_edit", methods={"GET", "POST"})
     */
    public function editfront(Request $request, Message $message, EntityManagerInterface $entityManager , SujetRepository $sujetRepository): Response
    {

        $form = $this->createForm(MessageFrontType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujet = $message->getIdSujet();
            $entityManager->flush();

            return $this->redirectToRoute('sujet_show_front', ['id'=> $sujet->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/editfront.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_delete", methods={"POST"})
     */
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/deletefront/{id}", name="message_front_delete", methods={"POST"})
     */
    public function deleteFront(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $sujet = $message->getIdSujet()->getId();
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $sujet = $message->getIdSujet();
            $sujet->setnbReponses($sujet->getnbReponses()-1);
            $entityManager->persist($sujet);
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sujet_show_front', array(  'id'=> $sujet), Response::HTTP_SEE_OTHER);
    }

}

