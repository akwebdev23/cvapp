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
    public function login(SerializeService $serializeService): Response
    {
        $user = $this->getUser();
        $user = $serializeService->serializeArray([$user])[0];
        $user->password = '';
        return $this->json([
                'user'  => $user,
                'status'  => 'ok',
                'messages' => 'Успешно!'
            ]);
    }
    /**
     * @Route("/api/get_user_auth", name="is_auth")
     */
    public function isAuth(SerializeService $serializeService)
    {
        $user = $this->getUser();
        if($user){
            $serializeUser = $serializeService->serializeArray([$user])[0];
            $user->password = '';

            return $this->json([
                'user'=>$user,
                'authSuccess'=>true,
                'status'=>'ok'
            ]);
        }
        return $this->json([
            'authSuccess'=>false,
            'status'=>'ok'
        ]);
    }
    /**
     * @Route("/api/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
