<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Service\SerializeService;
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
    /**
     * @Route("/api/get_user_auth", name="is_auth")
     */
    public function isAuth(SerializeService $serializeService)
    {
        $user = $this->getUser();
        if($user){
            $user = $serializeService->serializeArray([$user])[0];
            return $this->json([
                'user'=>$user,
                'message'=>'auth',
                'status'=>'ok'
            ]);
        }
        return $this->json([
            'message'=>'notauth',
            'status'=>'ok'
        ]);
    }
}
