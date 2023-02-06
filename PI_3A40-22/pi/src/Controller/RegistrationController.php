<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(MailerInterface $mailer , Request $request, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler, UsersAuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //$user->setRoles(array('ROLE_ADMIN'));
            $user->setImage("non");
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $string = $this->generateUrl('confirm', [
                'id' => $user->getId(),
            ]);

            $string = "localhost".$string;
            $email = (new Email())
                ->from('mahmoud.cheikh@esprit.tn')
                ->to('mahmoud.cheikh@esprit.tn')
                ->subject('Bienvenue sur notre site!')
                ->html('<a href='.$string.'>confirmer votre compte</a>');

            $mailer->send($email);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/registermobile" , name="register_mobile", methods={"GET"})
     */
    public function registermobile(Request $request,UserPasswordEncoderInterface $userPasswordEncoder,EntityManagerInterface $entityManager) : JsonResponse
    {
        $user = new Users();
        $user->setNom($request->get("nom"));
        $user->setPrenom($request->get("prenom"));
        $user->setEmail($request->get("email"));
        $user->setAdresse($request->get("adresse"));
        $user->setImage("confirme");
        $user->setPassword($userPasswordEncoder->encodePassword( $user, $request->get("password")));
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(true);
    }


}
