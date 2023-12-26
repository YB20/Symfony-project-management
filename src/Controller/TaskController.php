<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\DepartementRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    #[Route('/department/{id}/newtask', name: 'app_task')]
    public function new(Request $request, DepartementRepository $departementRepository, int $id, EntityManagerInterface $entityManagerInterface): Response
    {
        $t = new Task();
        $t->setName($request->get("name"));
        $t->setDescription($request->get("desc"));
        $t->setStatus("TO DO");
        $t->setCreateAt(new \DateTimeImmutable());
        $d = $departementRepository->find($id);
        $t->setDepartement($d);
        $entityManagerInterface->persist($t);
        $entityManagerInterface->flush();
        return $this->redirect("/departemt/$id");
    }

    #[Route('/department/{id}/updatetask/{idt}', name: 'app_update_task')]
    public function update(Request $request, TaskRepository $taskRepository, int $id, EntityManagerInterface $entityManagerInterface, int $idt): Response
    {
        $t = $taskRepository->find($idt);
        $t->setName($request->get("name"));
        $t->setDescription($request->get("desc"));
        $entityManagerInterface->persist($t);
        $entityManagerInterface->flush();
        return $this->redirect("/departemt/$id");
    }

    #[Route('/department/{id}/task/{idt}/assignuser', name: 'app_assign_user_to_task', methods: ['GET'])]
    public function assignUserToTask(Request $request, TaskRepository $taskRepository, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, int $id, int $idt): Response
    {
        $t = $taskRepository->find($idt);
        $u = $userRepository->find($request->get("user"));
        $t->setUser($u);
        $t->setStatus("DOING");
        $entityManagerInterface->persist($t);
        $entityManagerInterface->flush();
        return $this->redirect("/departemt/$id");
    }

    #[Route('/department/{id}/deletetask/{idt}', name: 'app_delete_task', methods: ['GET'])]
    public function delete(TaskRepository $taskRepository, EntityManagerInterface $entityManagerInterface, int $id, int $idt): Response
    {
        $t = $taskRepository->find($idt);
        $t->setUser(null);
        $entityManagerInterface->remove($t);
        $entityManagerInterface->flush();
        return $this->redirect("/departemt/$id");
    }

    #[Route('/department/{id}/task/{idt}/freeuser', name: 'app_free_user_from_task', methods: ['GET'])]
    public function freeUserFromTask(TaskRepository $taskRepository, EntityManagerInterface $entityManagerInterface, int $id, int $idt): Response
    {
        $t = $taskRepository->find($idt);
        $t->setUser(null);
        $t->setStatus("TO DO");
        $entityManagerInterface->persist($t);
        $entityManagerInterface->flush();
        return $this->redirect("/departemt/$id");
    }

    #[Route('/updatetask/{id}', name: 'app_update_task_from_user')]
    public function updateTaskFromUser(Request $request, TaskRepository $taskRepository, int $id, EntityManagerInterface $entityManagerInterface): Response
    {
        $t = $taskRepository->find($id);
        $t->setStatus($request->get("state"));
        $entityManagerInterface->persist($t);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_user_tasks");
    }
}
