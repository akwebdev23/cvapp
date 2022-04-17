<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Schema\Sequence;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/api/registration", name="app_registration")
     */
    public function index(
            Request $request, 
            ValidatorInterface $validator, 
            UserPasswordHasherInterface $hasher,
            UserRepository $userRep
        ): Response
    {
        $data = json_decode($request->getContent());
        $newUser = new User();
        $newUser->setEmail($data->email);
        $newUser->setPassword($hasher->hashPassword($newUser, $data->password));
        $newUser->setRoles([$data->roles]);
        $newUser->setName($data->name);
        $newUser->setPhone($data->phone);

        $errors = $validator->validate($newUser);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        $userRep->add($newUser, true);
        return $this->json([
            'message' => 'Registration success!',
            'status' => 'ok',
        ]);
    }
}
