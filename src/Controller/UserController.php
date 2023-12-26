<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/newuser', name: 'app_new_user', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface, RoleRepository $roleRepository): Response
    {
        $u = new User();
        $u->setUsername($request->get('username'));
        $u->setPassword($request->get('password'));
        $r = $roleRepository->find($request->get("role"));
        $u->setRole($r);
        $entityManagerInterface->persist($u);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_user");
    }

    #[Route('/updateuser/{id}', name: 'app_update_user', methods: ['GET'])]
    public function update(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, int $id, RoleRepository $roleRepository): Response
    {
        $u = $userRepository->find($id);
        $r = $roleRepository->find($request->get("role"));
        $u->setUsername($request->get('username'));
        $u->setPassword($request->get('password'));
        $r = $roleRepository->find($request->get("role"));
        $u->setRole($r);
        $entityManagerInterface->persist($u);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_user");
    }

    #[Route('/deleteuser/{id}', name: 'app_delete_user', methods: ['GET'])]
    public function delete(UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $u = $userRepository->find($id);
        $entityManagerInterface->remove($u);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_user");
    }
}
