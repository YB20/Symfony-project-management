<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractController
{
    #[Route('/roles', name: 'app_role')]
    public function index(RoleRepository $roleRepository): Response
    {
        $roles = $roleRepository->findAll();
        return $this->render('role/index.html.twig', [
            'roles' => $roles,
        ]);
    }

    #[Route('/newrole', name: 'app_new_role', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $r = new Role();
        $r->setName($request->get('name'));
        $entityManagerInterface->persist($r);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_role");
    }

    #[Route('/updaterole/{id}', name: 'app_update_role', methods: ['GET'])]
    public function update(Request $request, RoleRepository $roleRepository, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $r = $roleRepository->find($id);
        $r->setName($request->get('name'));
        $entityManagerInterface->persist($r);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_role");
    }

    #[Route('/deleterole/{id}', name: 'app_delete_role', methods: ['GET'])]
    public function delete(RoleRepository $roleRepository, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $r = $roleRepository->find($id);
        $entityManagerInterface->remove($r);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_role");
    }
}
