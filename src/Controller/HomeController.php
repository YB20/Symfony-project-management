<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, ProjetRepository $projetRepository, UserRepository $userRepository): Response
    {
        if ($request->getSession()->get("us") != null) {
            $projets = $projetRepository->findAll();
            $uu = $userRepository->find($request->getSession()->get("us")->getId());
            return $this->render('home/index.html.twig', [
                'projets' => $projets,
                'uu' => $uu
            ]);
        }

        return $this->redirectToRoute("app_login");
    }

    #[Route('/', name: 'app_home2')]
    public function index2(): Response
    {
        return $this->redirectToRoute("app_home");
    }

    #[Route('/login', name: 'app_login', methods: ["GET"])]
    public function login(Request $request): Response
    {
        if ($request->getSession()->get("us") != null) {
            return $this->redirectToRoute("app_home");
        }
        return $this->render('home/login.html.twig', []);
    }

    #[Route('/auth', name: 'app_auth', methods: ["POST"])]
    public function auth(Request $request, UserRepository $userRepository): Response
    {
        $u = $userRepository->findByUsernameAndPassword($request->get("username"), $request->get("password"));
        if ($u == null) {
            return $this->redirectToRoute("app_login");
        }
        $request->getSession()->set("us", $u);
        if ($u->getRole()->getId() != 2)
            return $this->redirectToRoute("app_home");
        return $this->redirectToRoute("app_user_tasks");
    }

    #[Route('/logout', name: 'app_logout', methods: ["GET"])]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();
        return $this->redirectToRoute("app_login");
    }

    #[Route('/my-tasks', name: 'app_user_tasks', methods: ["GET"])]
    public function userTasksPage(Request $request, TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findByUser($request->getSession()->get("us")->getId());
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
