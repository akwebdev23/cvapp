<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class FailureHandler implements AuthenticationFailureHandlerInterface
{
    function onAuthenticationFailure(Request $request, AuthenticationException $exception){
        return new JsonResponse(['messages'=>'Не правильно введен логин или пароль', 'status' => 'error']);
    }
}
