<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Stock;
use App\Form\CommandeFrontType;
use App\Form\ProduitType;
use App\Form\StockType;
use App\Repository\EmplacementRepository;
use App\Repository\ProduitRepository;
use App\Repository\QrCodeRepository;
use App\Repository\StockRepository;
use App\Repository\UsersRepository;
use App\Repository\VeloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Dompdf\Dompdf;
use Dompdf\Options;
/**
 * @Route("/stock")
 */
class StockController extends Controller
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct( EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="stock_index", methods={"GET"})
     */
    public function index(StockRepository $stockRepository): Response
    {
        $stock = $stockRepository->findAll();
        return $this->render('stock/index.html.twig',[
            'stocks' => $stock,
        ]);
    }
    private function getData(): array
    {
        /**
         * @var $stock Stock[]
         */
        $list = [];
        $stock = $this->entityManager->getRepository(Stock::class)->findOneBy($list);
        //$stock = $stockRepository->findAll();
        foreach ($stock as $stocks) {
            $list[] = [
                $stock->getId(),
                $stock->getLibelle(),
                $stock->getPrix(),
                $stock->getQuantite(),
                $stock->getDisponibilite(),
                $stock->getIdProduit()
            ];
        }
        return $list;
    }
    /**
     * @Route("/pdf", name="historique", methods={"GET"})
     */
    public function historique(StockRepository $stockRepository,ProduitRepository $produitRepository ,EmplacementRepository $emplacementRepository ): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', TRUE);
        $pdfOptions->set('image', '/public/img/logo.png');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->render('/stock/historique.html.twig', [
            'stocks' => $stockRepository->findAll(),
            'Produits' => $produitRepository->findAll(),
            'emplacements' => $emplacementRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();

        $dompdf->stream("historique.pdf", [
            "Attachment" => true
        ]);

        // Send some text response
       return $this->redirectToRoute('stock_index', [], Response::HTTP_SEE_OTHER);

    }

    /**
     * @Route("/new", name="stock_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stock);
            $entityManager->flush();

            return $this->redirectToRoute('stock_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="stock_show", methods={"GET"})
     */
    public function show(Stock $stock): Response
    {
        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }
//    /**
//     * @Route("/stat", name="stat" , methods={"GET"})
//     */

   /* public function stat(ProduitRepository $produitRepository  , StockRepository  $stockRepository): Response
    {
        $pieChart = new PieChart();

        $sommeVelo = 0;
        $sommePDR = 0;
        $sommeAccessoire = 0;

        $stock =$stockRepository->findAll();
        foreach ($stock as $stock) {
            if($this->getUser()->getId() == $stock->getIdUser()->getId()){
                if ($stock->getIdProduit()->getType() == "Velo"){
                    $sommeVelo = $sommeVelo +1;
                }
                if ($stock->getIdProduit()->getType() == "Accessoire"){
                    $sommeAccessoire = $sommeAccessoire +1;
                }
                if ($stock->getIdProduit()->getType() == "Piece de Rechange"){
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
        $pieChart->getOptions()->setTitle('Country Populations');
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->getLegend()->setPosition('none');
        $pieChart->getOptions()->setColors(['#e7711c']);
        $pieChart->getOptions()->getPieChart()->setLastBucketPercentile(10);
        $pieChart->getOptions()->getPieChart()->setBucketSize(10000000);

        return $this->render('stock/index.html.twig', [
            'pieChart' => $pieChart,
            'stocks' => $stockRepository->findAll(),
            'Produits' => $produitRepository->findAll(),
        ]);

    }
*/
    /**
     * @Route("/{id}/edit", name="stock_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('stock_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="stock_delete", methods={"POST"})
     */
    public function delete(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stock->getId(), $request->request->get('_token'))) {
            $entityManager->remove($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('stock_index', [], Response::HTTP_SEE_OTHER);
    }

 /*   /**
     * @Route("/stockmoins/{id}", name="stockmoins" , methods={"GET","POST"})
     */
   /* public function stockmoins(Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository , ProduitRepository $ProduitRepository,StockRepository $stockRepository ,$id,ProduitType $produitType  ): Response
    {
        $produit=$ProduitRepository->find($id);
        $Stock = $stockRepository->findAll();
        $qrCode = null;

        // $form =$produitType;
        //$data = $form->getImage();
        $qrCode = $codeRepository->qrcode($produit);

        $Commande = new Commande();
        $Stock  = new Stock();

        $form = $this->createForm(CommandeFrontType::class,$Commande);
        $from = $this->createForm(StockType::class, $Stock);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepository->find($this->getuser()->getid());
            $Commande->setIdUser($user);
            $Commande->setIdProduit($produit);
            $entityManager->persist($Commande);

            //STOCK CONTROLE
            //$stock = $stockRepository->findAll();
           //
            $Stock->setQuantite($Stock->getQuantite()-getnbProduits);
            //$event->setNbPlaces($event->getNbPlaces()-1);
            $entityManager->persist($Stock);

            $entityManager->flush();


            return $this->redirectToRoute('commandefront', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('/produit/ExploreProduit.html.twig', [
            'produit'=> $produit,

            'commande' => $Commande,

            'form' => $form->createView(),
        ]);
    }*/
}
