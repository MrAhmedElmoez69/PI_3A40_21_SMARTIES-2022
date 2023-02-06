<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Sujet;
use App\Form\ReclamationType;
use App\Form\RelcamationFrontFormType;
use App\Repository\ReclamationRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Date;
use Dompdf\Dompdf;
use Dompdf\Options;


/**
 * @Route("/reclamation")
 */
class ReclamationController extends AbstractController
{
    /**
     * @Route("/ajoutRec",name="ajoutRec" , methods={"POST","GET"})
     */
    public function ajoutRec(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = new Reclamation();
        $reclamation->setDescription($request->get('description'));
        $reclamation->setObjet($request->get('objet'));
        $reclamation->setDate(new \DateTime());
        $user = $usersRepository->find($request->get('idUser'));
        $reclamation->setIdUser($user);
        $em->persist($reclamation);
        $em->flush();
        $jsonContent = $normalizer->normalize($reclamation , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /**
     * @Route("/afficherSingleRec/{id}",name="afficherSingleRec" , methods={"POST","GET"})
     */
    public function afficherSingleRec(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository ,$id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        $jsonContent = $normalizer->normalize($reclamation , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /**
     * @Route("/afficherRec",name="afficherRec" , methods={"POST","GET"})
     */
    public function afficherRec(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->findAll();
        $jsonContent = $normalizer->normalize($reclamation , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /**
     * @Route("/modifierRec",name="modifierRec" , methods={"POST","GET"})
     */
    public function modifierRec(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $this->getDoctrine()->getManager()
            ->getRepository(Reclamation::class)
            ->find($request->get("id"));

        $description = $request->query->get("description");
        $objet = $request->query->get("objet");

        $reclamation->setDescription($description);
        $reclamation->setObjet($objet);

        $em->persist($reclamation);
        $em->flush();
        return new JsonResponse("Reclamation a ete modifiee avec success.");

    }

    /**
     * @Route("/deleteRec/{id}",name="deleteRec" , methods={"POST","GET"})
     */
    public function deleteRec(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository , $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        $em->remove($reclamation);
        $em->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/", name="reclamation_index", methods={"GET"})
     */
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/front", name="reclamation_front", methods={"GET"})
     */
    public function front(FlashyNotifier $flashy,ReclamationRepository $reclamationRepository,Request $request): Response
    {
        dump($request->get('search'));
        if (null !=$request->get('search')){
            $reclamations =$this->getDoctrine()->getRepository(Reclamation::class)->findBy(['id' => $request->get('search')]);
            return $this->render('/reclamation/front.html.twig',[
                'reclamations' => $reclamations,
                'flash'=>$request->get('flash'),
            ]);
        }

        $flashy->info('Reclamation Pasée', '');

        return $this->render('reclamation/front.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
            'flash'=> $request->get('flash'),
        ]);
    }



    /**
     * @Route("/pdfr", name="pdfr", methods={"GET"})
     */
    public function pdfr (ReclamationRepository $reclamationRepository , Request $request): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $reclamations = $reclamationRepository->findAll();

        $html = $this->renderView('/reclamation/pdf.html.twig',[
            'reclamations' => $reclamations,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Mes Reclamation.pdf", [
            "Attachment" => true
        ]);
    }


    /**
     * @Route("/trihazem", name="trihazem", methods={"GET"})
     */
    public function trihazem (ReclamationRepository $reclamationRepository , Request $request): Response
    {
        return $this->render('/reclamation/front.html.twig',[
            'reclamations' => $this->getDoctrine()->getRepository(Reclamation::class)->findBy([], ['id' => 'DESC']),
            'flash'=> $request->get('flash'),

        ]);
    }

    /**
     * @Route("/triha", name="triha", methods={"GET"})
     */
    public function triha (FlashyNotifier $flashy ,ReclamationRepository $reclamationRepository , Request $request): Response
    {

        if (null !=$request->get('search')){
            $reclamations =$this->getDoctrine()->getRepository(Reclamation::class)->findBy(['id' => $request->get('search')]);
            return $this->render('/reclamation/front.html.twig',[
                'reclamations' => $reclamations,
                'flash' =>$flashy,
            ]);
        }
        return $this->render('/reclamation/front.html.twig',[
            'reclamations' => $this->getDoctrine()->getRepository(Reclamation::class)->findBy([], ['id' => 'DESC']),
            'flash'=> $request->get('flash'),

        ]);
    }

    /**
     * @Route("/trid", name="trid", methods={"GET"})
     */
    public function trid (ReclamationRepository $reclamationRepository , Request $request): Response
    {

        if (null !=$request->get('search')){
            $reclamations =$this->getDoctrine()->getRepository(Reclamation::class)->findBy(['id' => $request->get('search')]);
            return $this->render('/reclamation/front.html.twig',[
                'reclamations' => $reclamations,
            ]);
        }
        return $this->render('/reclamation/front.html.twig',[
            'reclamations' => $this->getDoctrine()->getRepository(Reclamation::class)->findBy([], ['date' => 'DESC']),
            'flash'=> $request->get('flash'),
        ]);
    }

    /**
     * @Route("/newfront", name="reclamation_front_new", methods={"GET", "POST"})
     */
    public function newfront(Request $request, EntityManagerInterface $entityManager , UsersRepository $usersRepository): Response
    {

        $reclamation = new Reclamation();
        $form = $this->createForm(RelcamationFrontFormType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setDate(new \DateTime());
            $user = $usersRepository->find($this->getUser()->getid());
            $reclamation->setIdUser($user);
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $flash =1;

            return $this->redirectToRoute('reclamation_front', ['flash'=> 1], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new_front.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="reclamation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reclamation_show", methods={"GET"})
     */
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reclamation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reclamation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
