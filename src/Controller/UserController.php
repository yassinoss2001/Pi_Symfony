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
use App\Form\User\ResetPasswordType;
use App\Form\User\ChangePasswordType;
use App\Form\User\UpdateType;
use App\Security\LoginSuccessHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

#[Route('/user')]
class UserController extends AbstractController
{
    private $loginSuccessHandler;

    public function __construct(LoginSuccessHandler $loginSuccessHandler)
    {
        $this->loginSuccessHandler = $loginSuccessHandler;
    }
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
    public function login(Request $request, UserRepository $userRepository, AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher, TokenStorageInterface $tokenStorage)
    {
        $form = $this->createForm(LoginFormType::class);
    
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $password = $form->get('password')->getData();
            
            if ($email == 'ghaith.taieb01@gmail.com' && $password == 'Nowomennocry01') {
                return $this->redirectToRoute('app_users_admin');
            }
            
            $user = $userRepository->findOneBy(['email' => $email]);
            
            if ($user && $passwordHasher->isPasswordValid($user, $password) && $user->isVerified()) {$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                // Stocker le token dans le stockage
                $tokenStorage->setToken($token);
                // Utiliser le gestionnaire de succès d'authentification pour gérer la redirection
                return $this->loginSuccessHandler->onAuthenticationSuccess($request, $token);
            } else {
                return $this->redirectToRoute('app_login');
            }
        }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        return $this->render('user/login.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }
    

    #[Route('/forgetPassword', name: 'app_forgetPassword')]
    public function forgetPassword(Request $request,UserRepository $userRepository,ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $Form=$this->createForm(ResetPasswordType::class,$user);
        $Form->handleRequest($request);

        if ($Form->isSubmitted()){
            $user = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if($user){
                //generating verification code
                $verificationCode =rand(100000,1000000);
                $em = $doctrine->getManager();
                $user->setVerificationCode($verificationCode);
                $session = $request->getSession();
                $session->set('verification_code', $verificationCode);
                //sending verification code
                $transport = Transport::fromDsn('gmail+smtp://ghaith.taieb01%40gmail.com:axok%20vkse%20kvcq%20gvxp@smtp.gmail.com');
                $mailer = new Mailer($transport);
                $email = (new Email());
                $email->from('ghaith.taieb01@gmail.com');
                $email->to($user->getEmail());
                $email->subject('Reset password code');
                $email->html("<div>Voici le code de verification pour changer votre mot de passe : ".$verificationCode." </div>");
                $mailer->send($email);
       

                return $this->redirectToRoute('app_verifyCode', array('email' => $user->getEmail()));
            }
            return $this->render('user/forget_password.html.twig',array(
                'form'=>$Form->createView(),
                'UserNotFound'=>true
            ));
        }



        return $this->render('user/forget_password.html.twig',array(
            'form'=>$Form->createView()
        ));
    }
    #[Route('/verifyCode/{email}', name: 'app_verifyCode')]
    public function verifyCode(string $email,Request $request,UserRepository $userRepository,ManagerRegistry $doctrine): Response
    {

        return $this->render('user/verify_code.html.twig',array(
            'email'=>$email
        ));
    }

    #[Route('/validateCode', name: 'app_validate_code')]
public function validateCode(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine): Response
{
    $code = $request->query->get('code', 'null');
    $email = $request->query->get('email', 'null');
    $session = $request->getSession();
    $verif_code = $session->get('verification_code');
    // Vérifie si le code et l'email ne sont pas nuls
    if ($code !== 'null' && $email !== 'null') {
       
        $user = $userRepository->findOneBy(['email' => $email]);
        
        
        if ($user && $verif_code == $code) {
          
            return $this->redirectToRoute('app_resetPassword', ['email' => $email]);
        } else {
            
            return $this->redirectToRoute('app_verifyCode', ['email' => $email, 'error' => 'Invalid verification code']);
        }
    }

    // Redirige vers la vérification du code avec l'email
    return $this->redirectToRoute('app_verifyCode', ['email' => $email, 'error' => 'Invalid request']);
}


    #[Route('/resetPassword/{email}', name: 'app_resetPassword')]
    public function resetPassword(string $email,Request $request,UserRepository $userRepository,ManagerRegistry $doctrine,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $Form=$this->createForm(ChangePasswordType::class,$user);
        $Form->handleRequest($request);

        if ($Form->isSubmitted()){
            if($user->getPassword() === $user->getConfirmPassword()){
                $em = $doctrine->getManager();

                $userWithEmail = $userRepository->findOneBy(['email' => $email]);
                $hashedPassword = $passwordHasher->hashPassword($user,$user->getPassword());

                $userWithEmail->setPassword($hashedPassword);

                $em->flush();
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('user/resetPassword.html.twig',
            array(
                'email'=>$email,
                'form'=>$Form->createView()
            ));
    }
    #[Route('/dashboard/admin', name: 'app_users_admin')]
    public function DisplayUsersAdmin(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/display_admin.html.twig', array(
            'users'=>$users,
        ));
    }
    #[Route('/profil', name: 'app_profil')]
public function profil(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine, SessionInterface $session): Response
{

    return $this->render('user/profileUser.html.twig');
}
#[Route('/users/delete', name: 'app_users_delete')]
    public function delete(Request $request,UserRepository $userRepository,ManagerRegistry $doctrine): Response
    {
        $id = $request->query->get('id', 'null');
        if($id!='null'){
            $em = $doctrine->getManager();
            $user = $userRepository->find($id);
            $em->remove($user);
            $em->flush();
            return $this->redirectToRoute('app_users_admin');
        }
        return $this->redirectToRoute('app_users_admin');
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method is empty, since Symfony's logout functionality is handled automatically by the security system.
    }
    #[Route('/users/update/{id}', name: 'app_users_update')]
    public function Update(int $id,Request $request,UserRepository $userRepository,ManagerRegistry $doctrine,ValidatorInterface $validator): Response
    {


            $user=$userRepository->find($id);
            $Form=$this->createForm(UpdateType::class,$user);
            $Form->handleRequest($request);

            $errors = $validator->validate($user);

            if(count($errors) > 0){
                return $this->render('user/update.html.twig', array(
                    'userform'=>$Form->createView(),
                    'errors'=>$errors
                ));
            }

            if ($Form->isSubmitted()&&$Form->isValid())/*verifier */
            {
                $em=$doctrine->getManager();
               

                $userWithSameEmail = $userRepository->findOneBy(['email' => $user->getEmail()]);
                

                if($userWithSameEmail && $userWithSameEmail->getId()!=$user->getId()){
                    return $this->render('user/update.html.twig', array(
                        'userform'=>$Form->createView(),
                        'errors'=>array(),
                        'sameEmail'=>true
                    ));
                }



                $em->flush();

                return $this->redirectToRoute('app_users_admin');
            }

            return $this->render('user/update.html.twig', array(
                'userform'=>$Form->createView(),
                'errors'=>array()
            ));
    }
   


}
