<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\Users;
use App\Form\CommandeType;
use App\Form\CommandeFrontType;
use App\Repository\AchatRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UsersRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;




/**
 * @Route("/commande")
 */
class CommandeController extends Controller
{

    /********************Json for activites**********************/
    /**
     * @Route("/afficherCommande",name="afficherCommadne")
     */
    public function afficherCommande(CommandeRepository $commandeRepository, NormalizerInterface $normalizer){

        $Commande = $this->getDoctrine()->getManager()->getRepository(Commande::class)->findAll();
        $jsonContent = $normalizer->normalize($Commande , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/displayall",name="displayall" , methods={"POST","GET"})
     */
    public function displaya(Request $request, NormalizerInterface $normalizer, UsersRepository $usersRepository): JsonResponse
    {
        $user = $usersRepository->find($request->get('idUser'));
        $Commande = $this->getDoctrine()->getManager()->getRepository(Commande::class)->findBy(['idUser'=> $user]);
        $jsonContent = $normalizer->normalize($Commande , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/displaycommande/{id}",name="display_single_achat" , methods={"POST","GET"})
     */
    public function displayCommande(Request $request, NormalizerInterface $normalizer , $id): JsonResponse
    {
        $Commande = $this->getDoctrine()->getManager()->getRepository(Commande::class)->find($id);
        $jsonContent = $normalizer->normalize($Commande , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/ajoutMobilecomm",name="ajoutMobilecomm" , methods={"POST","GET"})
     */
    public function ajoutMobilecommande(Request $request, NormalizerInterface $normalizer ,CommandeRepository $commandeRepository,UsersRepository $usersRepository, ProduitRepository $produitRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $Commande = new Commande();
        $user = $usersRepository->find($request->get('idUser'));
        $Commande->setIdUser($user);
        $Commande->setNbProduits($request->get('NbProduits'));
        $produit = $produitRepository->find($request->get('idProduit'));
        $Commande->setIdProduit($produit);
        $em->persist($Commande);
        $em->flush();
        $jsonContent = $normalizer->normalize("commande ajouté avec succés", 'json', ['groups' => 'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/modifiermobilecomm",name="modifierMobilecomm" , methods={"POST","GET"})
     */
    public function modMobilecommande(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository ,  ProduitRepository $produitRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $Commande = $em->getRepository(Commande::class)->find($request->get('id'));
        $Commande->setNbProduits($request->get('NbProduits'));
        $em->persist($Commande);
        $em->flush();
        $jsonContent = $normalizer->normalize($Commande , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/deletemobilecomm/{id}",name="deleteMobilecomm" , methods={"POST","GET"})
     */
    public function deleteMobilecommande(Request $request, NormalizerInterface $normalizer , $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $Commande = $em->getRepository(Commande::class)->find($id);
        $em->remove($Commande);
        $em->flush();
        $jsonContent = $normalizer->normalize($Commande , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }



    /**
     * @Route("/", name="commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $CommandeRepository): Response
    {
       /* return $this->render('Commande/index.html.twig', [
            'Commandes' => $CommandeRepository->findAll(),
        ]);*/
        return $this->render('Commande/index.html.twig', [
            'Commandes' => $CommandeRepository->findAll(),
        ]);

    }


    /**
     * @Route("/commandefront", name="commandefront" , methods={"GET"})
     */

    public function ahmed_a(CommandeRepository $CommandeRepository , ProduitRepository $produitRepository): Response
    {
        $pieChart = new PieChart();

        $sommeVelo = 0;
        $sommePDR = 0;
        $sommeAccessoire = 0;

        $commande =$CommandeRepository->findAll();
        foreach ($commande as $commande) {
            if($this->getUser()->getId() == $commande->getIdUser()->getId()){
                if ($commande->getIdProduit()->getType() == "Velo"){
                    $sommeVelo = $sommeVelo +1;
                }
                if ($commande->getIdProduit()->getType() == "Accessoire"){
                    $sommeAccessoire = $sommeAccessoire +1;
                }
                if ($commande->getIdProduit()->getType() == "Piece de Rechange"){
                    $sommePDR = $sommePDR+1;
                }
            }

        }

        $pieChart->getData()->setArrayToDataTable(
            [['Type', 'Nombre'],
                ['Velo',     $sommeVelo],
                ['Piece de rechange',      $sommePDR],
                ['Accessoire',  $sommeAccessoire],
            ]
        );
        $pieChart->getOptions()->setTitle('Mes Commandes');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setIs3D(true);
        $pieChart->getOptions()->setWidth(1850);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(false);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#ACEB1E');
        $pieChart->getOptions()->setColors(['#333', '#CB2326', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#CB2326', '#6AF9C4']);
        $pieChart->getOptions()->setBackgroundColor("transparent");
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Montserrat');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);



        return $this->render('/commande/commandefront.html.twig', [
            'Commandes' => $CommandeRepository->findAll(),
            'Produits' => $produitRepository->findAll(),
            'pieChart' => $pieChart,


        ]);
    }



    /**
     * @Route("/achatfront/", name="achatfront",  methods={"GET"})
     */
    public function achatfront(FlashyNotifier $flashy,AchatRepository $achatRepository, CommandeRepository $commandeRepository, ProduitRepository $produitRepository ,EntityManagerInterface $entityManager,Request $request): Response
    {


        //dump($request->get('search'));
        if (null !=$request->get('search')){
            $achats =$this->getDoctrine()->getRepository(Achat::class)->findBy(['id' => $request->get('search')]);
            return $this->render('/commande/achatfront.html.twig',[
                'achats' => $achats,
                'flash'=> $request->get('flash'),
            ]);
        }
        $flashy->success('Achat effectué', '');

        $achats =$achatRepository->findAll();
        $achats = $this->get('knp_paginator')->paginate(
            $achats,
            $request->query->getInt('page',1),
            9
        );

        return $this->render('/commande/achatfront.html.twig', [
            'achats' => $achats,
            'Produits' => $produitRepository->findAll(),
            'flash'=> $request->get('flash'),

        ]);

    }



    /**
     * @Route("/achatf/{id}", name="achat_delete_front", methods={"POST" , "GET"})
     */
    public function achat_delete_front(Request $request, Achat $achat, EntityManagerInterface $entityManager , int $id , Commande $commande): Response
    {
        $entityManager->remove($achat);
        $entityManager->flush();
        $idC = $commande->getId();
        dump($commande);
        dump($idC);
        return $this->redirectToRoute('achatfront', array('id' => $idC), Response::HTTP_SEE_OTHER);
    }





    /**
     * @Route("/new", name="commande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager ,UsersRepository $usersRepository ): Response
    {
        $Commande = new Commande();
        $form = $this->createForm(CommandeType::class, $Commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepository->find($this->getuser()->getid());
            $Commande->setIdUser($user);
            $entityManager->persist($Commande);
            $entityManager->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Commande/new.html.twig', [
            'commande' => $Commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newFront", name="commande_newFront", methods={"GET", "POST"})
     */
    public function newF(Request $request, EntityManagerInterface $entityManager,UsersRepository $usersRepository): Response
    {
        $Commande = new Commande();
        $form = $this->createForm(CommandeFrontType::class, $Commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepository->find($this->getuser()->getid());
            $Commande->setIdUser($user);
            $entityManager->persist($Commande);

            $entityManager->flush();

            return $this->redirectToRoute('commandefront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Commande/newFront.html.twig', [
            'commande' => $Commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/testpdf" , name="testpdf")
     */
public function testpdf(CommandeRepository $commandeRepository , ProduitRepository $produitRepository)
    {

    return $this->render('/commande/pdf.html.twig', [
        'Commandes' => $commandeRepository->findAll(),
        'Produits' => $produitRepository->findAll(),
    ]);
}



    /**
     * @Route("/pdfc", name="pdfc", methods={"GET"})
     */
        public function pdfc (AchatRepository $AchatRepository, CommandeRepository $commandeRepository , ProduitRepository $produitRepository, Request $request): Response
     {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);


         $html = $this->render('/commande/pdf.html.twig', [
             'Commandes' => $commandeRepository->findAll(),
             'Produits' => $produitRepository->findAll(),
         ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("MyCommnade.pdf", [
            "Attachment" => true
        ]);

    }



    /**
     * @Route("/{id}", name="commande_show", methods={"GET"})
     */
    public function show(Commande $Commande): Response
    {
        return $this->render('Commande/show.html.twig', [
            'commande' => $Commande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Commande $Commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $Commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Commande/edit.html.twig', [
            'commande' => $Commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_delete", methods={"POST"})
     */
    public function deleteback(Request $request, Commande $Commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($Commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/commandef/{id}", name="commande_delete_front", methods={"POST" , "GET"})
     */
    public function deletefront(Request $request, Commande $Commande, EntityManagerInterface $entityManager , int $id): Response
    {
        $entityManager->remove($Commande);
        $entityManager->flush();


        return $this->redirectToRoute('commandefront', [], Response::HTTP_SEE_OTHER);
    }
/*
    /**
     * @Route("/commandeupdate/{id}", name="commande_update_front", methods={"POST" , "GET"})
     */
  //  public function updatefront(int $nb,CommandeRepository  $commandeRepository,Request $request, Commande $Commande, EntityManagerInterface $entityManager , int $id): Response
//    {

        //$test = $commandeRepository->find($id);
       // $test->setNbProduits($nb);
      //  $entityManager->persist($test);
    //    $entityManager->flush();


  //      return $this->redirectToRoute('commandefront', [], Response::HTTP_SEE_OTHER);
//    }


    /**
     * @Route("/{id}/editF", name="commande_update_front", methods={"GET", "POST"})
     */
    public function updatefront(Request $request, Commande $Commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeFrontType::class, $Commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('commandefront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Commande/updateFront.html.twig', [
            'commande' => $Commande,
            'form' => $form->createView(),
        ]);
    }





}





