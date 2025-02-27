<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {   
        $user = $userRepository->findAllUsers();
        return $this->render('user/index.html.twig', [
            'users' => $user,
        ]);
    }
    #[Route('/user/{id}/edit', name: 'app_user_edit')]
    public function edit(User $user, UserRepository $userRepository,  EntityManagerInterface $entityManager, Request $request): Response
    {
        if($request->isMethod('POST')){
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setAdresse($request->request->get('adresse'));
            // $user->setRoles($request->request->get('roles'));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user');
        }

        //roles
        $roles = $userRepository->allRoles();
        
        return $this->render('useredit/index.html.twig', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }
}
