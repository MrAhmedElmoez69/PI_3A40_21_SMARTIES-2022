<?php

namespace App\Controller;

use App\Entity\Emplacement;
use App\Entity\Evenement;
use App\Entity\Favoris;
use App\Entity\Message;
use App\Entity\Produit;
use App\Entity\Stock;
use App\Entity\Users;
use App\Repository\AchatRepository;
use App\Repository\CommandeRepository;
use App\Repository\AccessoireRepository;
use App\Repository\EmplacementRepository;
use App\Repository\FavorisRepository;
use App\Repository\ProduitRepository;
use App\Repository\StockRepository;
use App\Repository\SujetRepository;
use App\Repository\UsersRepository;
use App\Repository\VeloRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SiteController extends AbstractController
{


    /**
     * @Route("/test/", name="siteback")
     */
    public function test(): Response
    {
        return $this->render('/base_back.html.twig');
    }

    /**
     * @Route("/", name="site")
     */
    public function front(ProduitRepository $produitRepository,SujetRepository $sujetRepository): Response
    {

        if($this->getUser()!= null){


       if ($this->getUser()->getUsername() == "ADMIN@ADMIN.COM"){
           return $this->redirectToRoute("siteback");
        }
        }

        $produits = $produitRepository->findAll();
        $produits = array_slice($produits ,0 , 4);
        $sujets= $sujetRepository->findAll();
        $sujets = array_slice($sujets ,0 , 3);

        return $this->render('/base_front.html.twig',[
            'produits' => $produits,
            'sujets'=>$sujets,
        ]);
    }

    /**
     * @Route("/commandefront", name="ahmed" , methods={"GET"})
     */

    public function ahmed_b(CommandeRepository $CommandeRepository): Response
    {
        return $this->render('/commande/commandeFront.html.twig', [
            'Commandes' => $CommandeRepository->findAll(),

        ]);
    }

    /**
     * @Route("/fadwa/", name="fadwa")
     */
    public function fadwa(): Response
    {
        return $this->render('/abonnement/fadwa.html.twig');
    }

        /**
         * @Route("/ahmed/", name="ahmed")
         */
        public function ahmed(): Response
    {
        return $this->render('/commande/newFront.html.twig');
    }



    /*
    /**
     * @Route("/produitfront", name="mariem")
     *//*
    public function mariem_e(ProduitRepository $ProduitRepository,VeloRepository $veloRepository,AccessoireRepository $accessoireRepository): Response
    {
        return $this->render('/produit/mariem_front.html.twig', [
            'Produits' => $ProduitRepository->findAll(),
            'velos' => $veloRepository->findAll(),
            'accessoires' => $accessoireRepository->findAll(),
        ]);
    }
*/

    /**
     * @Route("/mahmoud/", name="mahmoud")
     */
    public function mahmoud(): Response
    {
        return $this->render('/users/mahmoud.html.twig');
    }

    /**
     * @Route("/hazem/", name="hazem")
     */
    public function hazem(): Response
    {
        return $this->render('/piecedr/hazem.html.twig');
    }

    /**
     * @Route("/autre/", name="autre")
     */
    public function autre(): Response
    {
        return $this->render('/autre.html.twig');
    }
    /**
     * @Route("/triparcapacite", name="triparcapacite", methods={"GET"})
     */
    public function triparcapacite (EmplacementRepository $emplacementRepository,Request $request):Response
    {
        return $this->render('/emplacement/site_front.html.twig',[
            'emplacements' => $this->getDoctrine()->getRepository(Emplacement::class)->findBy([], ['capacite' => 'DESC']),
        ]);
    }
    /**
     * @Route("/trirSites", name="trirSites", methods={"GET"})
     */
    public function trirSites(EmplacementRepository $emplacementRepository,Request $request):Response
    {
        return $this->render('/emplacement/site_front.html.twig',[
            'emplacements' => $this->getDoctrine()->getRepository(Emplacement::class)->findBy([], ['lieu' => 'ASC']),
        ]);
    }
    /****************************************************** PRODUIT *******************************************************************************************************/

    /********************Json for products**********************/
    /**
     * @Route("/display",name="display", methods={"POST","GET"})
     */
    public function display(Request $request, NormalizerInterface $normalizer,SerializerInterface  $serializer ): JsonResponse
    {
       $produit = $this->getDoctrine()->getManager()->getRepository(Produit::class)->findAll();
        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($produit);
        return  new JsonResponse($formatted);*/
        $jsonContent = $normalizer->normalize($produit , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }


/*********************************JSON for add***************************************************/
    /**
     * @Route("/AjouterProduit",name="AjouterProduit")
     */
    public function AjouterProduit(Request $request){
        $prod = new Produit();
        $libelle = $request->query->get("libelle");
        $description = $request->query->get("description");
        $type = $request->query->get("type");
        $prix = $request->query->get("prix");
        $image = $request->query->get("image");


        $em =$this->getDoctrine()->getManager();

        $prod->setLibelle($libelle);
        $prod->setDescription($description);
        $prod->setType($type);
        $prod->setPrix($prix);
        $prod->setImage($image);

        /*if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
            try {
                $imageFile->move(
                    'img\bike',
                    $newFilename
                );
            } catch (FileException $e) {
            }
            $Produit->setImage($newFilename);
        }*/

        $em->persist($prod);
        $em->flush();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($prod);
        return  new JsonResponse($formatted);
    }

    /******************JSON delete Produit*****************************************/
    /**
     * @Route("/deleteProd/{id}", name="deleteProd")
     */

    public function deleteProd(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Produit::class)->find($id);
        if($prod!=null ) {
            $em->remove($prod);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Produit a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Produit invalide.");
    }

    /*************************************JSON UPDATE produit*******************************************************/

    /**
     * @Route("/updateProduit", name="updateProduit")
     */
    public function updateProduit(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $prod = $this->getDoctrine()->getManager()
            ->getRepository(Produit::class)
            ->find($request->get("id"));

        $libelle = $request->query->get("libelle");
        $image = $request->query->get("image");
        $description = $request->query->get("description");
        $prix = $request->query->get("prix");
        $type = $request->query->get("type");

        $prod->setLibelle($libelle);
        $prod->setImage($image);
        $prod->setDescription($description);
        $prod->setPrix($prix);
        $prod->setType($type);

        $em->persist($prod);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($prod);
        return new JsonResponse("Produit a ete modifiee avec success.");

    }

    /****************************************************** EMPLACEMENT *******************************************************************************************************/

    /********************Json for Emplacement**********************/
    /**
     * @Route("/displayEmplacement",name="displayEmplacement")
     */
    public function displayEmplacement(Request $request, NormalizerInterface $normalizer,SerializerInterface  $serializer ): JsonResponse
    {
        $emp = $this->getDoctrine()->getManager()->getRepository(Emplacement::class)->findAll();
        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($produit);
        return  new JsonResponse($formatted);*/
        $jsonContent = $normalizer->normalize($emp , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /******************Modifier Emplacement*****************************************/
    /**
     * @Route("/updateEmplacement", name="updateEmplacement")
     */
    public function updateEmplacement(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $emp = $this->getDoctrine()->getManager()
            ->getRepository(Emplacement::class)
            ->find($request->get("id"));

        $lieu = $request->query->get("lieu");
        $capacite = $request->query->get("capacite");
        //$Stock = $request->query->get("Stock");

        $emp->setLieu($lieu);
        $emp->setCapacite($capacite);
        //$emp->setStok($Stock);

        $em->persist($emp);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($emp);
        return new JsonResponse("Emplacement a ete modifiee avec success.");

    }
    /********************delete for Emplacement**********************/
    /**
     * @Route("/deleteEmplacement/{id}", name="deleteEmplacement")
     */

    public function deleteEmplacement(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $emp = $em->getRepository(Emplacement::class)->find($id);
        if($emp!=null ) {
            $em->remove($emp);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("emplacement a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Emplacement invalide.");
    }
    /*********************************add Emplacement***************************************************/
    /**
     * @Route("/AjouterEmplacement",name="AjouterEmplacement")
     */
    public function AjouterEmplacement(Request $request){
        $emp = new Emplacement();

        $lieu = $request->query->get("lieu");
        $capacite = $request->query->get("capacite");


        $em =$this->getDoctrine()->getManager();

        $emp->setLieu($lieu);
        $emp->setCapacite($capacite);

        $em->persist($emp);
        $em->flush();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($emp);
        return  new JsonResponse($formatted);
    }
/****************************************************** STOCK *******************************************************************************************************/

    /********************Json for Stock**********************/
    /**
     * @Route("/displayStock",name="displayStock")
     */
    public function displayStock(Request $request, NormalizerInterface $normalizer,SerializerInterface  $serializer ): JsonResponse
    {
        $stock = $this->getDoctrine()->getManager()->getRepository(Stock::class)->findAll();
        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($produit);
        return  new JsonResponse($formatted);*/
        $jsonContent = $normalizer->normalize($stock , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);
    }
    /******************Modifier Emplacement*****************************************/
    /**
     * @Route("/updateStock", name="updateStock", methods={"POST","GET"})
     */
    public function updateStock(Request $request , StockRepository  $stockRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $stock = new Stock();
        $stock = $stockRepository->find($request->get("id"));

        $stock->setLibelle($request->get("libelle"));
        $stock->setPrix($request->get("prix"));
        $stock->setDisponibilite($request->get("disponibilite"));

        $em->persist($stock);
        $em->flush();

        return new JsonResponse("stock a ete modifiee avec success.");

    }
    /********************delete for stock**********************/
    /**
     * @Route("/deleteStock/{id}", name="deleteStock")
     */

    public function deleteStock(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $stock = $em->getRepository(Stock::class)->find($id);
        if($stock!=null ) {
            $em->remove($stock);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Stock a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Stock invalide.");
    }
    /*********************************add Stock***************************************************/
    /**
     * @Route("/AjouterStock",name="AjouterStock")
     */
    public function AjouterStock(Request $request, NormalizerInterface $normalizer , UsersRepository $usersRepository, ProduitRepository $produitRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $stock = new Stock();
        /*$user = $usersRepository->find($request->get('idUser'));
        $message->setIdUser($user);
        $message->setDate(new \DateTime());*/
        $produit = $produitRepository->find($request->get('idProduit'));
        $stock->setIdProduit($produit);
        $libelle = $request->query->get("libelle");
        $prix = $request->query->get("prix");
        $quantite = $request->query->get("quantite");
        $disponibilite = $request->query->get("disponibilite");

        $stock->setLibelle($libelle);
        $stock->setPrix($prix);
        $stock->setQuantite($quantite);
        $stock->setDisponibilite($disponibilite);
        $em->persist($stock);
        $em->flush();
        $jsonContent = $normalizer->normalize("msg ajouté",'json', ['groups' => 'post:read']);
        return new JsonResponse($jsonContent);
    }

    /**************************************************FAVORIS***************************************************************************/
    /********************Json for favoris**********************/
    /**
     * @Route("/displayFavoris",name="displayFavoris", methods={"POST","GET"})
     */
    public function displayFavoris(SerializerInterface  $serializer,FavorisRepository $repository,Request $request, NormalizerInterface $normalizer ): JsonResponse
    {

        /*$produit = $this->getDoctrine()->getManager()->getRepository(Produit::class)->find($id);

        $fav = $this->getDoctrine()->getManager()->getRepository(Favoris::class)->findBy(['IdProduit' => $produit]);
        $jsonContent = $normalizer->normalize($fav , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($jsonContent);*/
        return $this->json(
            json_decode(
                $serializer->serialize(
                    $repository->findAll(),
                    'json',
                    ['groups'=>'post:read']
                ),
                JSON_OBJECT_AS_ARRAY
            )
        );
    }

    /********************Json for favoris**********************/
    /**
     * @Route("/searchP",name="searchP")
     */
    public function searchP(Request $request){
        $produit = $request->query->get("libelle");
        $prod = $this->getDoctrine()->getManager()->getRepository(Produit::class)->findBy(['libelle' => $produit]);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($prod);
        return  new JsonResponse($formatted);
    }


}
