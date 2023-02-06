<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Form\UsersFrontType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{

    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $UsersRepository): Response
    {
        return $this->render('Users/index.html.twig', [
            'Users' => $UsersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="users_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/creation", name="users_new_front", methods={"GET", "POST"})
     */
    public function creation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersFrontType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('site', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Users/createfront.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('Users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/front/", name="users_front_show", methods={"GET" , "POST"})
     */
    public function showFront(): Response
    {
        return $this->render('Users/showFront.html.twig');
    }

    /**
     * @Route("/resetpass/", name="resetpass", methods={"GET" ,  "POST"})
     */
    public function resetpass(MailerInterface $mailer ,Request $request , UsersRepository $usersRepository ,EntityManagerInterface $entityManager): Response
    {
        if (null !=$request->get('email')){
            $user = $usersRepository->findBy(['email' => $request->get('email')]);
            $user = array_shift($user);

            if($user != null){
                $string = $this->generateUrl('reset', [
                    'id' => $user->getId(),
                ]);

                $user->setRole("reset");
                $entityManager->persist($user);
                $entityManager->flush();


                $string = "localhost".$string;
                $email = (new Email())
                    ->from('mahmoud.cheikh@esprit.tn')
                    ->to('mahmoud.cheikh@esprit.tn')
                    ->subject('Reinitialisation mot de passe!')
                    ->html('<a href='.$string.'>reinitialiser votre mot de passe</a>');

                $mailer->send($email);
            }


        }
        return $this->render('Users/resetpass.html.twig');
    }

    /**
     * @Route("/resetmobile", name="resetmobile", methods={"GET"})
     */
    public function resetmobile(MailerInterface $mailer ,Request $request , UsersRepository $usersRepository ,EntityManagerInterface $entityManager): JsonResponse
    {
        if (null !=$request->get('email')){
            $user = $usersRepository->findBy(['email' => $request->get('email')]);
            $user = array_shift($user);

            if($user != null){
                $string = $this->generateUrl('reset', [
                    'id' => $user->getId(),
                ]);

                $random = $random = random_int(100000000, 999999999);
                $user->setRole($random);
                $entityManager->persist($user);
                $entityManager->flush();


                $string = "localhost".$string;
                $email = (new Email())
                    ->from('mahmoud.cheikh@esprit.tn')
                    ->to('mahmoud.cheikh@esprit.tn')
                    ->subject('Reinitialisation mot de passe!')
                    ->html('<a href='.$string.'>votre code de reinitialisation est :'.$random.'</a>');

                $mailer->send($email);
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/resetpass", name="resetpass", methods={"GET" , "POST"})
     */
    public function mobilepassword(UserPasswordEncoderInterface $userPasswordEncoder,Request $request,UsersRepository $usersRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if (null !=$request->get('password')) {
            $user = $usersRepository->findBy(['role' => $request->get('code')]);
            $user = array_shift($user);

            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $request->get('password')
                )
            );
            $user->setRole("non");
            $user->setImage("confirme");
            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse(false);
    }
    /**
     * @Route("/reset/{id}", name="reset", methods={"GET" , "POST"})
     */
    public function reset(UserPasswordEncoderInterface $userPasswordEncoder,Request $request,UsersRepository $usersRepository,int $id, EntityManagerInterface $entityManager): Response
    {
        if (null !=$request->get('pass')) {
            $user = $usersRepository->find($id);

            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $request->get('pass')
                )
            );
            $user->setRole("non");
            $entityManager->persist($user);
            $entityManager->flush();
            $user->setImage("confirme");
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('site');
        }



        return $this->render('users/reset.html.twig' , ['userid'=>$id]);
    }

    /**
     * @Route("/confirmer/{id}", name="confirm", methods={"GET" , "POST"})
     */
    public function confirm(UsersRepository $usersRepository,int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $usersRepository->find($id);
        $user->setImage("confirme");
        $entityManager->persist($user);
        $entityManager->flush();

        return  $this->redirectToRoute('site');
    }

    /**
     * @Route("/modifierfront", name="users_modifier_front", methods={"GET", "POST"})
     */
    public function modifierFront(Request $request, EntityManagerInterface $entityManager , UsersRepository $usersRepository): Response
    {
        $user = $usersRepository->find($this->getUser()->getId());
        $form = $this->createForm(UsersFrontType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users_front_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Users/modifierFront.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="users_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ban/{id}", name="banUser", methods={"POST" , "GET"})
     */
    public function ban(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $user->setImage("banned");
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/unban/{id}", name="unbanUser", methods={"POST" , "GET"})
     */
    public function unban(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $user->setImage("confirme");
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/{id}", name="users_delete", methods={"POST"})
     */
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }




}
