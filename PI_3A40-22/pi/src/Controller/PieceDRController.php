<?php

namespace App\Controller;

use App\Entity\PieceDR;
use App\Form\PieceDRType;
use App\Repository\PieceDRRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/piecedr")
 */
class PieceDRController extends AbstractController
{
    /**
     * @Route("/piecedr_front", name="pieceder" , methods={"GET"})
     */
    public function hazem(PieceDRRepository $pieceDRRepository): Response
    {
        return $this->render('/piece_dr/piecedr_front.html.twig',[
            'piece_d_rs' => $pieceDRRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="piece_d_r_index", methods={"GET"})
     */
    public function index(PieceDRRepository $pieceDRRepository): Response
    {
/*        return $this->render('piece_dr/index.html.twig', [
            'piece_d_rs' => $pieceDRRepository->findAll(),
        ]);
*/
        return $this->render('piece_dr/hazem.html.twig', [
            'piece_d_rs' => $pieceDRRepository->findAll(),
        ]);
    }




    /**
     * @Route("/new", name="piece_d_r_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pieceDR = new PieceDR();
        $form = $this->createForm(PieceDRType::class, $pieceDR);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $new=$form->getData();
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        'img',
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $pieceDR->setImage($newFilename);
            }
            $entityManager->persist($pieceDR);
            $entityManager->flush();


            $entityManager->persist($pieceDR);
            $entityManager->flush();

            return $this->redirectToRoute('piece_d_r_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('piece_dr/new.html.twig', [
            'piece_d_r' => $pieceDR,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="piece_d_r_show", methods={"GET"})
     */
    public function show(PieceDR $pieceDR): Response
    {
        return $this->render('piece_dr/show.html.twig', [
            'piece_d_r' => $pieceDR,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="piece_d_r_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, PieceDR $pieceDR, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PieceDRType::class, $pieceDR);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('piece_d_r_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('piece_dr/edit.html.twig', [
            'piece_d_r' => $pieceDR,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="piece_d_r_delete", methods={"POST"})
     */
    public function delete(Request $request, PieceDR $pieceDR, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pieceDR->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pieceDR);
            $entityManager->flush();
        }

        return $this->redirectToRoute('piece_d_r_index', [], Response::HTTP_SEE_OTHER);
    }
}
