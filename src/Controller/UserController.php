<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/users', name: 'app_user_api', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository): JsonResponse
    {   
        $users = $userRepository->findAll();
        $data=[];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'Last_Name' => $user->getNom(),
                'First_Name' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'adresse' => $user->getAdresse(),
                'roles' => $user->getRoles(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/user/{id}', name: 'app_user_api_id', methods: ['PUT'])]
    public function modifUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {   
        $user = $userRepository->find($id);
        if(!$user){
            return $this->json(['message' => 'User not found'], 404);
        }
        $data= json_decode($request->getContent(), true);
        if(isset($data['nom'])){
            $user->setNom($data['nom']);
        }
        if(isset($data['prenom'])){
            $user->setPrenom($data['prenom']);
        }
        if(isset($data['adresse'])){
            $user->setAdresse($data['adresse']);
        }
        if(isset($data['roles'])){
            $user->setRoles($data['roles']);
        }
        $entityManager->flush();

        return $this->json(['message' => 'User updated']);
    }
}
