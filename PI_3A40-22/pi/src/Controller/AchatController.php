<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Commande;
use App\Form\AchatType;
use App\Repository\AchatRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/achat")
 */
class AchatController extends AbstractController
{

    /**
     * @Route("/modifiermobileachat",name="modifierMobileachat" , )
     */
    public function modMobileachat(Request $request, NormalizerInterface $normalizer , AchatRepository $achatRepository): JsonResponse
    {

        $em = $this->getDoctrine()->getManager();
        $achat = $achatRepository->find($request->get("id"));
        $nomclient= $request->query->get("nomClient");
        $numclient=$request->query->get("numeroClient");
        $achat->setNomClient($nomclient);
        $achat->setnumeroClient($numclient);
        $em->persist($achat);
        $em->flush();
        return new JsonResponse("modif effectuer");
    }

    /**
     * @Route("/deletemobileachat",name="deleteMobileachat" , methods={"POST","GET"})
     */
    public function deleteMobileachat(Request $request, NormalizerInterface $normalizer ): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $achat = $em->getRepository(Achat::class)->find($request->get('id'));
        $em->remove($achat);
        $em->flush();
        $jsonContent = $normalizer->normalize($achat , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /********************Json for activites**********************/
    /**
     * @Route("/afficherAchat",name="afficherAchat")
     */
    public function afficherAchat(AchatRepository $repository, NormalizerInterface $normalizer){

        $achat = $this->getDoctrine()->getManager()->getRepository(Achat::class)->findAll();
        $jsonContent = $normalizer->normalize($achat , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /**
     * @Route("/displayachats",name="displayachat" , methods={"POST","GET"})
     */
    public function displayone(Request $request, NormalizerInterface $normalizer, UsersRepository $usersRepository): JsonResponse
    {
        $user = $usersRepository->find($request->get('idUser'));
        $achat = $this->getDoctrine()->getManager()->getRepository(Achat::class)->findBy(['idUser'=> $user]);
        $jsonContent = $normalizer->normalize($achat , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/displayachat/{id}",name="display_single_achat" , methods={"POST","GET"})
     */
    public function displayachats(Request $request, NormalizerInterface $normalizer , $id): JsonResponse
    {
        $achat = $this->getDoctrine()->getManager()->getRepository(Achat::class)->find($id);
        $jsonContent = $normalizer->normalize($achat , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/ajoutmobileachat",name="ajoutMobileachat" , methods={"POST","GET"})
     */
    public function ajoutMobileachat(Request $request, NormalizerInterface $normalizer ,AchatRepository $AchatRepository , UsersRepository $usersRepository , ProduitRepository $produitRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $achat = new Achat();
        $achat->setDate(new \DateTime());
        $nomclient=$request->query->get("nomClient");
        $numclient=$request->query->get("numeroClient");
        $user = $usersRepository->find($request->get('idUser'));
        $achat->setIdUser($user);
        $achat->setnumeroClient($numclient);
        $achat->setnomClient($nomclient);
        $produit = $produitRepository->find($request->get('idProduit'));
        $achat->setidProduit($produit);
        $em->persist($achat);
        $em->flush();
        $jsonContent = $normalizer->normalize($achat , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }






    /**
     * @Route("/", name="achat_index", methods={"GET"})
     */
    public function index(AchatRepository $achatRepository): Response
    {
        return $this->render('achat/index.html.twig', [
            'achats' => $achatRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="achat_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository): Response
    {
        $achat = new Achat();
        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepository->find($this->getuser()->getid());
            $achat->setIdUser($user);
            $entityManager->persist($achat);
            $entityManager->flush();

            return $this->redirectToRoute('achat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('achat/new.html.twig', [
            'achat' => $achat,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/frontnew/{id}", name="achat_front_new", methods={"GET", "POST"})
     */
    public function newFront(int $id,Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository,CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);
        $achat = new Achat();
        $user = $usersRepository->find($this->getuser()->getid());
        $achat->setIdUser($user);
        $achat->setIdProduit($commande->getIdProduit());
        $achat->setDate(new \DateTime());
        $achat->setNomClient($user->getNom());
        $achat->setNumeroClient($commande->getNbProduits());
        $entityManager->persist($achat);
        $entityManager->flush();

        $entityManager->remove($commande);
        $entityManager->flush();

        $flash = 1;
        return $this->redirectToRoute('achatfront',['flash'=> 1], Response::HTTP_SEE_OTHER);



    }



    /**
     * @Route("/tricc", name="tricc", methods={"GET"})
     */
    public function tricc (FlashyNotifier $flashy, AchatRepository $AchatRepository , Request $request): Response
    {
        if (null !=$request->get('search')){
            $achats =$this->getDoctrine()->getRepository(Achat::class)->findBy(['id' => $request->get('search')]);
            return $this->render('/commande/achatfront.html.twig',[
                'achats' => $achats,
                'flash' =>$flashy,

            ]);
        }

        return $this->render('/commande/achatfront.html.twig',[
            'achats' => $this->getDoctrine()->getRepository(Achat::class)->findBy([], ['numeroClient' => 'DESC']),
            'flash'=> $request->get('flash'),

        ]);
    }

    /**
     * @Route("/tri", name="tri", methods={"GET"})
     */
    public function tri (FlashyNotifier $flashy,AchatRepository $AchatRepository , Request $request): Response
    {
        if (null !=$request->get('search')){
            $achats =$this->getDoctrine()->getRepository(Achat::class)->findBy(['id' => $request->get('search')]);
            return $this->render('/commande/achatfront.html.twig',[
                'achats' => $achats,
                'flash' => $flashy,
            ]);
        }

        return $this->render('/commande/achatfront.html.twig',[
            'achats' => $this->getDoctrine()->getRepository(Achat::class)->findBy([], ['date' => 'ASC']),
            'flash'=> $request->get('flash'),

        ]);
    }

    /**
     * @Route("/{id}", name="achat_show", methods={"GET"})
     */
    public function show(Achat $achat): Response
    {
        return $this->render('achat/show.html.twig', [
            'achat' => $achat,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="achat_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Achat $achat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('achat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('achat/edit.html.twig', [
            'achat' => $achat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="achat_delete", methods={"POST"})
     */
    public function delete(Request $request, Achat $achat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $achat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($achat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('achat_index', [], Response::HTTP_SEE_OTHER);
    }



}
