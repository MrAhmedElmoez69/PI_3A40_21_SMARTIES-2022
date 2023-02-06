<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use App\Repository\ActiviteRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
/**
 * @Route("/activite")
 */
class ActiviteController extends AbstractController
{


    /********************Json for activites**********************/
    /**
     * @Route("/afficherAct",name="afficherAct")
     */
    public function afficherAct(ActiviteRepository $repository,Request $request, SerializerInterface  $serializer, EvenementRepository $evenementRepository)
    {

        $event = $evenementRepository->find($request->get('event'));
        return $this->json(
            json_decode(
                $serializer->serialize(
                    $repository->findBy(['idEvenement' => $event]),
                    'json',
                    [AbstractNormalizer::IGNORED_ATTRIBUTES => ['idEvenement']]
                ),
                JSON_OBJECT_AS_ARRAY
            )
        );
    }
    /********************Json for activites**********************/
    /**
     * @Route("/afficherActt",name="afficherActt")
     */
    public function afficherActt(ActiviteRepository $repository, SerializerInterface  $serializer){
        return $this->json(
            json_decode(
                $serializer->serialize(
                    $repository->findAll(),
                    'json',
                    [AbstractNormalizer::IGNORED_ATTRIBUTES => ['idEvenement']]
                ),
                JSON_OBJECT_AS_ARRAY
            )
        );
    }
    /******************Ajouter Activite*****************************************/
    /**
     * @Route("/addAct", name="addAct")
     */
    public function ajouter(Request $request, NormalizerInterface $normalizer , EvenementRepository  $eventRepository): JsonResponse
{
$em = $this->getDoctrine()->getManager();
$activite = new Activite();
    /*$user = $usersRepository->find($request->get('idUser'));
    $message->setIdUser($user);
    $message->setDate(new \DateTime());*/
$event = $eventRepository->find($request->get('idEvenement'));
    $activite->setIdEvenement($event);
    $nom = $request->query->get("nom");
    $description = $request->query->get("description");
    $image = $request->query->get("image");
    $em = $this->getDoctrine()->getManager();

    $activite->setNom($nom);
    $activite->setDescription($description);
    $activite->setImage($image);
$em->persist($activite);
$em->flush();
$jsonContent = $normalizer->normalize("msg ajouté",'json', ['groups' => 'post:read']);
return new JsonResponse($jsonContent);
}
    /******************delete Activite*****************************************/
    /**
     * @Route("/deleteAct/{id}", name="deleteAct")
     */

    public function deleteAct(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $activite = $em->getRepository(Activite::class)->find($id);
        if($activite!=null ) {
            $em->remove($activite);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("activite a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id activite invalide.");


    }
    /******************Modifier activite*****************************************/
    /**
     * @Route("/updateAct", name="updateAct")
     */
    public function modifierAct(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $activite = $this->getDoctrine()->getManager()
            ->getRepository(Activite::class)
            ->find($request->get("id"));

        $nom = $request->query->get("nom");
        $description = $request->query->get("description");
        $image = $request->query->get("image");
        $activite->setNom($nom);
        $activite->setDescription($description);
        $activite->setImage($image);
        $em->persist($activite);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($activite);
        return new JsonResponse("Activite a ete modifiee avec success.");

    }
    /**
     * @Route("/", name="activite_index", methods={"GET"})
     */
    public function index(ActiviteRepository $activiteRepository): Response
    {
        return $this->render('activite/index.html.twig', [
            'activites' => $activiteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="activite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $form->getData();
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        'img\bike',
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $activite->setImage($newFilename);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($activite);
                $entityManager->flush();

                return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
            }}

            return $this->render('activite/new.html.twig', [
                'activite' => $activite,
                'form' => $form->createView(),
            ]);
        }

        /**
         * @Route("/{id}", name="activite_show", methods={"GET"})
         */
        public
        function show(Activite $activite): Response
        {
            return $this->render('activite/show.html.twig', [
                'activite' => $activite,
            ]);
        }

        /**
         * @Route("/{id}/edit", name="activite_edit", methods={"GET", "POST"})
         */
        public
        function edit(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
        {
            $form = $this->createForm(ActiviteType::class, $activite);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $new = $form->getData();
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    try {
                        $imageFile->move(
                            'img\bike',
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }
                    $activite->setImage($newFilename);
                }
                $entityManager->flush();

                return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('activite/edit.html.twig', [
                'activite' => $activite,
                'form' => $form->createView(),
            ]);
        }

    /**
     * @Route("/{id}", name="activite_delete", methods={"POST"})
     */
    public function delete(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($activite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
    }

}
