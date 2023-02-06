<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementFrontType;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Route("/abonnement")
 */
class AbonnementController extends AbstractController
{
    /**
     * @Route("/deleteA/{id}", name="deleteA")
     */

    public function deleteA(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $abonnement = $em->getRepository(Abonnement::class)->find($id);
        if($abonnement!=null ) {
            $em->remove($abonnement);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("abonnement a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id abonnement invalide.");


    }
    /********************Json for abonnement**********************/



    /**
     * @Route("/afficherA",name="afficherA")
     */
    public function afficherA(AbonnementRepository $repository, SerializerInterface  $serializer){
        return $this->json(
            json_decode(
                $serializer->serialize(
                    $repository->findAll(),
                    'json',
                    [AbstractNormalizer::IGNORED_ATTRIBUTES => ['idUser']]
                ),
                JSON_OBJECT_AS_ARRAY
            )
        );
    }
    /******************Ajouter Abonnement*****************************************/
    /**
     * @Route("/addA", name="addA")
     */

    public function ajouter(Request $request)
    {
        $abonnement = new Abonnement();
        $type = $request->query->get("type");
        $dateD = $request->query->get("dateD");
        $dateF = $request->query->get("dateF");
        $prix=$request->query->get("prix");
        $em = $this->getDoctrine()->getManager();
        $dateD = new \DateTime('now');
        $dateF = new \DateTime('now');

        $abonnement->setType($type);
        $abonnement->setDated($dateD);
        $abonnement->setDatef($dateF);
        $abonnement->setPrix($prix);

        $em->persist($abonnement);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($abonnement);

        return new JsonResponse($formatted);

    }
    /******************delete Abonnement*****************************************/

    /******************Modifier event*****************************************/
    /**
     * @Route("/updateA", name="updateA")
     */
    public function modifierA(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $abonnement = $this->getDoctrine()->getManager()
            ->getRepository(Abonnement::class)
            ->find($request->get("id"));

        $type = $request->query->get("type");
        $dateD = $request->query->get("dateD");
        $dateF = $request->query->get("dateF");
        $prix=$request->query->get("prix");

         $abonnement->setType($type);
       // $abonnement->setDated($dateD);
        //$abonnement->setDatef($dateF);
        $abonnement->setPrix($prix);
        $dateD = new \DateTime('now');
        $dateF = new \DateTime('now');
        $em->persist($abonnement);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($abonnement);
        return new JsonResponse("Abonnement a ete modifiee avec success.");

    }

    /**
     * @Route("/", name="abonnement_index", methods={"GET"})
     */
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        /*return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);*/
        return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }
    /**
     * @Route("/triabonnement", name="triabonnement", methods={"GET"})
     */
    public function triabonnement(AbonnementRepository $abonnementRepository, Request $request): Response
    {

        $abonnement = $this->getDoctrine()->getRepository(Abonnement ::class)->findBy([], ['datef' => 'ASC']);

        return $this->render('/abonnement/front.html.twig', [
            'abonnements' => $abonnement,
        ]);
    }
    /**
     * @Route("/new", name="abonnement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/front", name="abonnement_front_index", methods={"GET"})
     */
    public function indexFront(AbonnementRepository $abonnementRepository,Request $request): Response
    {
        if (null != $request->get('search') ) {
        $abonnement = $this->getDoctrine()->getRepository(Abonnement::class)->findBy(['type' => $request->get('search')]);
        return $this->render('/abonnement/front.html.twig', [
            'abonnements' => $abonnement,
        ]);
    }
        return $this->render('abonnement/front.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/newfront", name="abonnement_front_new", methods={"GET", "POST"})
     */
    public function newFront(Request $request, EntityManagerInterface $entityManager ,UsersRepository  $usersRepository): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementFrontType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnement->setIdUser($this->getUser());
            $abonnement->setDated(new \DateTime());

            $abonnement->setPrix(20);

            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('abonnement_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonnement/newfront.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="abonnement_show", methods={"GET"})
     */
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="abonnement_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="abonnement_delete", methods={"POST"})
     */
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('abonnement_index', [], Response::HTTP_SEE_OTHER);
    }
}