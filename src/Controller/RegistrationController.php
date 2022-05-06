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
        $newUser->setPassword($data->password);
        $newUser->setName($data->name ? $data->name : $data->email);

        $failData = $validator->validate($newUser);
        $errorMessages = [];
        foreach ($failData as $value) {
            $errorMessages[] = $value->getMessage();
        }
        if (count($errorMessages) > 0) {
            return $this->json([
                'messages' => $errorMessages,
                'status' => 'validation_fail',
            ]);
        }
        $userRep->add($newUser, true);
        return $this->json([
            'messages' => 'Пользователь '.$newUser->getName().' успешно зарегистрирован!',
            'status' => 'ok',
        ]);
    }
}
