<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



class ApiUser extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, NormalizerInterface $normalizer): Response
    {
        // Récupération des données du formulaire
        $email = $request->request->get('email');
        $password = $request->request->get('pwd');

        // Contrôle de saisie
        if (!$email || !$password) {
            return $this->json([
                'status' => 'error',
                'message' => 'Please provide email and password',
            ]);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        // Utilisateur non trouvé
        if (!$user) {
            return $this->json([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }

        // Mot de passe incorrect
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid password',
            ]);
        }
        

        // Authentification réussie
        $connectedUser = $normalizer->normalize($user, 'json', ['groups' => "users"]);
        return $this->json([
            'status' => 'success',
            'message' => 'Logged Successfully',
            'user' => $connectedUser
        ]);
    }

    #[Route('/api/signup', name: 'api_signup', methods: ["POST"])]
    public function signup(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();

        // Récupération des données du formulaire
        $email = $request->request->get('email');
        $password = $request->request->get('pwd');
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $confirmPassword = $request->request->get('confirmPwd');
        $roles = 'ROLE_USER';

        // Contrôle de saisie
        if (!$email || !$password || !$nom || !$prenom || !$confirmPassword) {
            return $this->json(['status' => 'error', 'message' => 'Please provide all required fields']);
        }

        // Vérification de correspondance entre mot de passe et confirmation
        if ($password !== $confirmPassword) {
            return $this->json(['status' => 'error', 'message' => 'Passwords do not match']);
        }

        // Hachage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword(null, $password);

        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setIsVerified(false); // Utilisateur non vérifié par défaut

        // Persist et flush dans la base de données
        $em->persist($user);
        $em->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Registered Successfully'
        ]);
    }
}



