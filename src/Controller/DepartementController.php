<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Task;
use App\Repository\DepartementRepository;
use App\Repository\ProjetRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DepartementController extends AbstractController
{
    #[Route('/project/{idp}/newdepartement', name: 'app_new_departement', methods: ['GET'])]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface, int $idp, ProjetRepository $projetRepository): Response
    {
        $p = $projetRepository->find($idp);
        $d = new Departement();
        $d->setName($request->get("name"));
        $d->setDescription($request->get("desc"));
        $d->setProjet($p);
        $entityManagerInterface->persist($d);
        $entityManagerInterface->flush();
        return $this->redirect("/project/$idp");
    }

    #[Route('/project/{idp}/updatedepartment/{id}', name: 'app_update_department', methods: ['GET'])]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, int $id, int $idp, DepartementRepository $departementRepository): Response
    {
        $d = $departementRepository->find($id);
        $d->setName($request->get('name'));
        $d->setDescription($request->get('desc'));
        $entityManagerInterface->persist($d);
        $entityManagerInterface->flush();
        return $this->redirect("/project/$idp");
    }

    #[Route('/project/{idp}/deletedepartemt/{id}', name: 'app_delete_department', methods: ['GET'])]
    public function delete(DepartementRepository $departementRepository, EntityManagerInterface $entityManagerInterface, int $id, int $idp): Response
    {
        $d = $departementRepository->find($id);
        $entityManagerInterface->remove($d);
        $entityManagerInterface->flush();
        return $this->redirect("/project/$idp");
    }

    #[Route('/departemt/{id}', name: 'app_department')]
    public function index(DepartementRepository $departementRepository, int $id, UserRepository $userRepository): Response
    {
        $dept = $departementRepository->find($id);
        $users = $userRepository->findUsersWithoutDepartment();
        return $this->render('departement/index.html.twig', [
            'dept' => $dept,
            'id' => $id,
            'users' => $users,
        ]);
    }

    #[Route('/department/{id}/adduser', name: 'app_user_to_department', methods: ['GET'])]
    public function addUserToDepartment(Request $request, DepartementRepository $departementRepository, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $d = $departementRepository->find($id);
        $u = $userRepository->find($request->get("user"));
        $u->setDepartement($d);
        $entityManagerInterface->persist($d);
        $entityManagerInterface->flush();
        return $this->redirect("/departemt/$id");
    }

    #[Route('/department{id}/removeuser/{idc}', name: 'app_remove_user_from_department', methods: ['GET'])]
    public function removeUserFromDepartment(TaskRepository $taskRepository, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, int $id, int $idc): Response
    {
        $u = $userRepository->find($idc);
        $tasks = $u->getTasks();
        foreach ($tasks as $task) {
            $t = $taskRepository->find($task->getId());
            $t->setUser(null);
            $t->setStatus("TO DO");
            $entityManagerInterface->persist($u);
            $entityManagerInterface->flush();
        }
        $u->setDepartement(null);
        $entityManagerInterface->persist($u);
        $entityManagerInterface->flush();
        return $this->redirect("/departemt/$id");
    }
}
