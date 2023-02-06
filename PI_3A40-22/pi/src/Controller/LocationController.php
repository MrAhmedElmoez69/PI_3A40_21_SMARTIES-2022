<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationFrontType;
use App\Form\LocationType;
use App\Repository\AbonnementRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/location")
 */
class LocationController extends AbstractController
{
    /**
     * @Route("/", name="location_index", methods={"GET"})
     */
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render('location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/trilocationdate", name="trierpartypelocation", methods={"GET"})
     */
    public function trierpartype(LocationRepository  $locationRepository , Request $request): Response
    {

        $location = $locationRepository->findBy([], ['date' => 'ASC']);
        return $this->render('/location/front.html.twig', [
            'locations' => $location,
        ]);
    }


    /**
     * @Route("/new", name="location_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('location/new.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route ("/pdfl" , name="pdfl")
     */
    public function pdfl(LocationRepository $locationRepository )
    {

        return $this->render('/commande/pdf.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }
    /**
     * @Route("/pdflocation", name="pdflocation", methods={"GET"})
     */
    public function pdflocation(LocationRepository $locationRepository , Request $request): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);


        $html = $this->render('/location/pdflocation.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("MesLocations.pdf", [
            "Attachment" => true
        ]);

    }


    /**
     * @Route("/front", name="location_front_index", methods={"GET"})
     */
    public function indexfront(LocationRepository $locationRepository, Request $request): Response
    {
        if (null != $request->get('search') ) {

            $location = $this->getDoctrine()->getRepository(Location::class)->findBy(['id' => $request->get('search')]);
            return $this->render('/location/front.html.twig', [
                'locations' => $location,
            ]);
        }
        return $this->render('location/front.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/frontnew/{id}", name="location_front_new", methods={"GET", "POST"})
     */
    public function newfront(Request $request, EntityManagerInterface $entityManager , int $id , AbonnementRepository $abonnementRepository): Response
    {
        $abonnement = $abonnementRepository->find($id);
        $location = new Location();
        $form = $this->createForm(LocationFrontType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $location->setIdUser($this->getUser());
            $location->setIdAbonnement($abonnement);
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('location_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('location/newfront.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="location_show", methods={"GET"})
     */
    public function show(Location $location): Response
    {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="location_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('location/edit.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="location_delete", methods={"POST"})
     */
    public function delete(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
            $entityManager->remove($location);
            $entityManager->flush();
        }

        return $this->redirectToRoute('location_index', [], Response::HTTP_SEE_OTHER);
    }






}
