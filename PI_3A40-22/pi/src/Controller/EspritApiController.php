<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Location;
use App\Entity\Users;
use App\Form\LocationFrontType;
use App\Form\LocationType;
use App\Repository\AbonnementRepository;
use App\Repository\LocationRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
/**
 * @Route("/espritApi")
 */
class EspritApiController extends AbstractController
{

  /*  public function allLocation()
    {

        $formations = $this->getDoctrine()
            ->getRepository(Location::class)
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($formations);
        return new JsonResponse($formatted);
    }
*/

    /**
     * @Route("/allLocation", methods={"GET"})
     */
    public function allLoc()
    {

        $locations = $this->getDoctrine()
            ->getRepository(Location::class)
            ->findAll();
        $jsonContent = null;
        $i = 0;
        $Location = new Location();
        foreach ($locations as $location) {
            $jsonContent[$i]['id'] = $location->getId();
            $jsonContent[$i]['Date'] = $location->getDate()->format('Y-m-d');
            $jsonContent[$i]['Heure'] = $location->getHeure()->format('H:i:s');
            $jsonContent[$i]['Duree'] = $location->getDuree();

           $jsonContent[$i]['idUser'] = $location->getIdUser();
         //   $jsonContent[$i]['idAbonnement'] = $location->getIdAbonnement();


            $i++;
        }


        $json = json_encode($jsonContent);
        return new Response($json);


    }

/*
    public function allLocation() {
          $em= $this->getDoctrine()->getManager();
          $location = $em->getRepository(Location::class)->findAll();
          //response from server
          $encoder = new JsonEncoder();
          $normalizer = new ObjectNormalizer();
          //detect error

          $normalizer->setCircularReferenceLimit(1);
          $normalizer->setCircularReferenceHandler(function ($object){
              if(method_exists($object, 'getId')){
                  return $object->getId();
              }
          });


          $serializer= new Serializer([$normalizer], [$encoder]);
          $formatted = $serializer->normalize($location);
          return new JsonResponse($formatted);

      }
*/
    /**
     * @Route("/newLocation", name="location_create")
     */

    public  function newLocation(request $request , \Symfony\Component\Serializer\Normalizer\NormalizerInterface $normalizer , UsersRepository $usersRepository , AbonnementRepository $abonnementRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $Date= new \DateTime(urldecode($request->get("Date")));
        $Heure=new \DateTime(urldecode($request->get("Heure")));
        $Duree= $request->get("Duree");
        //  $idAbonnement = $request->get("idAbonnement");

        $location = new Location();
        $location->setDate($Date);
        $location->setHeure($Heure);
        $location->setDuree($Duree);
        $user = $usersRepository->find($request->get('IdUser'));
        $abo= $abonnementRepository->find($request->get('IdAbonnement'));
        //   $location->setIdAbonnement($em->getRepository(Abonnement::class)->find(1));
        $location->setIdUser($user);
        $location->setIdAbonnement($abo);

        $em->persist($location);
        $em->flush();
        $serializer = new Serializer([new DateTimeNormalizer(),new ObjectNormalizer()]);
        $formatted = $normalizer->normalize($location , 'json' , ['groups'=>'post:read']);
        return new JsonResponse($formatted);

    }


    /**
     * @Route("/updateLocation", name="location_modiiiiiff")
     */
    public function updateLocation(request $request){
        $em = $this->getDoctrine()->getManager();
        $idLocation= $request->get("id");


        $location=  $em->getRepository(Location::class)->find($idLocation);


        $Date= new \DateTime(urldecode($request->get("Date")));
        $Heure=new \DateTime(urldecode($request->get("Heure")));
        $Duree= $request->get("Duree");


        $location->setDate($Date);
        $location->setHeure($Heure);
        $location->setDuree($Duree);


        $em->persist($location);
        $em->flush();
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object){
            return $object->getId();
        });
        // $serializer = new Serializer([$normalizer],[$encoder]);
        //   $formatted = $serializer->normaliz($location);
        return new JsonResponse("Location updated with success");

    }

    /**
     * @Route("/deleteLocation/{id}", name="location_supp")
     */
    public function deleteLocation($id){
        $em = $this->getDoctrine()->getManager();
        $location = $em->getRepository(Location::class)->find($id);
        $em->remove($location);
        $em->flush();
        return new JsonResponse("location deleted");
    }

}