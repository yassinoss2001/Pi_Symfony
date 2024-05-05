<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;
    private $authorizationChecker;

    public function __construct(UrlGeneratorInterface $router,AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
{
    // Récupérer l'utilisateur actuellement authentifié
    $user = $token->getUser();

    // Récupérer le rôle de l'utilisateur
    $roles = $user->getRoles();
    

    // Déterminer la redirection en fonction du rôle de l'utilisateur
    
        if (in_array('LIVREUR', $roles)) {
            
        $targetUrl = $this->router->generate('app_home');
    } elseif (in_array('GERANT', $roles)) {
        $targetUrl = $this->router->generate('app_home');
    }elseif (in_array('ADMIN', $roles)) {
        $targetUrl = $this->router->generate('app_users_admin');
    }
    elseif (in_array('CLIENT', $roles)) {
        $targetUrl = $this->router->generate('app_home');
    }
     else {
        $targetUrl = $this->router->generate('app_login');
    }

    return new RedirectResponse($targetUrl);
}

}
