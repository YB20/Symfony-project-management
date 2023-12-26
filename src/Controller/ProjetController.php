<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Repository\DepartementRepository;
use App\Repository\ProjetRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjetController extends AbstractController
{

    #[Route('/newproject', name: 'app_new_projet', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $p = new Projet();
        $p->setName($request->get('name'));
        $p->setDescription($request->get('desc'));
        $p->setCreatedAt(new \DateTimeImmutable());
        $dl = new DateTime($request->get('deadline'));
        $p->setDeadline($dl);
        $p->setState("PENDING");
        $entityManagerInterface->persist($p);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_home");
    }

    #[Route('/updateproject/{id}', name: 'app_update_projet', methods: ['GET'])]
    public function update(Request $request, ProjetRepository $projetRepository, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $p = $projetRepository->find($id);
        $p->setName($request->get('name'));
        $p->setDescription($request->get('desc'));
        $dl = new DateTime($request->get('deadline'));
        $p->setDeadline($dl);
        $p->setState($request->get('state'));
        $entityManagerInterface->persist($p);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_home");
    }

    #[Route('/deleteproject/{id}', name: 'app_delete_projet', methods: ['GET'])]
    public function delete(ProjetRepository $projetRepository, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $p = $projetRepository->find($id);
        $entityManagerInterface->remove($p);
        $entityManagerInterface->flush();
        return $this->redirectToRoute("app_home");
    }

    #[Route('/project/{id}', name: 'app_project_home')]
    public function projectDetails(ProjetRepository $projetRepository, $id, UserRepository $userRepository): Response
    {
        $p = $projetRepository->find($id);
        $u = $userRepository->findByRole(3);
        $departements = $p->getDepartements();
        return $this->render('home/project.html.twig', [
            'departements' => $departements,
            'idp' => $id,
            'project' => $p,
            'chef' => $u,
        ]);
    }

    #[Route('/project/{idp}/chefproject', name: 'app_add_chef_project', methods: ['GET'])]
    public function addChefProject(Request $request, ProjetRepository $projetRepository, $idp, EntityManagerInterface $entityManagerInterface, UserRepository $userRepository): Response
    {
        $p = $projetRepository->find($idp);
        $u = $userRepository->find($request->get("chef"));
        $p->setChef($u);
        $entityManagerInterface->persist($p);
        $entityManagerInterface->flush();
        return $this->redirect("/project/$idp");
    }

    #[Route('/project/{idp}/removechef', name: 'app_remove_chef_project', methods: ['GET'])]
    public function removeChefProject(Request $request, ProjetRepository $projetRepository, $idp, EntityManagerInterface $entityManagerInterface): Response
    {
        $p = $projetRepository->find($idp);
        $p->setChef(null);
        $entityManagerInterface->persist($p);
        $entityManagerInterface->flush();
        return $this->redirect("/project/$idp");
    }
}
