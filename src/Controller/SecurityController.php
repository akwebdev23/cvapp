<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/login", name="app_login")
     */
    public function login(): Response
    {
        $user = $this->getUser();
        return $this->json([
                'user'  => $user ? $user->getEmail() : null,
            ]);
    }
}
