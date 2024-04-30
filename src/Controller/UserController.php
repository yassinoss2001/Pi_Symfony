<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\User\LoginFormType;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/signup', name: 'app_add_user')]
    public function Add(MailerInterface $mailer, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Création d'une nouvelle instance d'utilisateur
        $user = new User();
        $user->setVerificationCode("");
        $user->setIsVerified(false);
        $Form = $this->createForm(UserType::class, $user);
        $Form->handleRequest($request);

        $errors = $validator->validate($user);

        if(count($errors) > 0){
            return $this->render('user/create.html.twig', array(
                'userform'=>$Form->createView(),
                'errors'=>$errors
            ));
        }

        
        if ($Form->isSubmitted() && $Form->isValid()) {
            $em = $doctrine->getManager();

            // Vérification si l'email existe déjà dans la base de données
            $userWithSameEmail = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($userWithSameEmail) {
                return $this->render('user/create.html.twig', [
                    'userform' => $Form->createView(),
                    'errors' => [],
                    'sameEmail' => true
                ]);
            }

            // Hachage du mot de passe avant de le stocker dans la base de données
            if ($user->getPassword() !== null) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
            }

            // Enregistrement de l'utilisateur
            $em->persist($user);
            $em->flush();

            // Envoi du lien de vérification par email
            $verificationLink = $this->generateUrl('app_verifyEmail', ['email' => $user->getEmail()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
            $transport = Transport::fromDsn('gmail+smtp://ghaith.taieb01%40gmail.com:axok%20vkse%20kvcq%20gvxp@smtp.gmail.com');
            $mailer = new Mailer($transport);
            $email = (new Email())
                ->from('ghaith.taieb01@gmail.com')
                ->to($user->getEmail())
                ->subject('Vérification de votre compte')
                ->html("<p>Pour vérifier votre compte, veuillez cliquer sur le lien suivant : <a href='{$verificationLink}'>Vérifier mon compte</a></p>");

            $mailer->send($email);

           
            return $this->render('user/create.html.twig', array(
                'userform'=>$Form->createView(),
                'errors'=>array(),
                'message'=>"Presque terminé, Pour finaliser votre inscription à YummyFood, il nous suffit de vérifier votre adresse e-mail. Nous venons d'envoyer un lien de confirmation à votre email"
            ));
        }

        return $this->render('user/create.html.twig', [
            'userform' => $Form->createView(),
            'errors' => $validator->validate($user)
        ]);
    }
    #[Route('/verifyEmail/{email}', name: 'app_verifyEmail')]
    public function verifyEmail(string $email,UserRepository $userRepository,ManagerRegistry $doctrine): Response
    {
        $user = $userRepository->findOneBy(['email' => $email]);
        if($user){
            $user->setisVerified(1);
            $em=$doctrine->getManager();
            $em->flush();

            return $this->render('user/verified.html.twig', array(
                'status'=>'success',
            ));
        }
        return $this->render('user/verified.html.twig', array(
            'status'=>'error',
        ));
    }
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, UserRepository $userRepository,AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $form = $this->createForm(LoginFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneBy(['email' => $user->getEmail()]);

            if (!$user) {
                $this->addFlash('danger', 'Email address not found');
                return $this->redirectToRoute('app_login');
            }

            if (!$passwordHasher->isPasswordValid($user, $user->getPassword())) {
                $this->addFlash('danger', 'Invalid password');
                return $this->redirectToRoute('app_login');
            }

            // Authentication successful
            $this->addFlash('success', 'Welcome '.$user->getEmail());
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'lastUsername'=> $lastUsername,
            'error' => $error,
        ]);
    }
    

}
