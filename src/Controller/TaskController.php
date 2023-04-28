<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;

use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function PHPUnit\Framework\isEmpty;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_tasks_list')]
    public function listAction(EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour créer une tâche.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('task/list.html.twig', [
            'controller_name' => 'TaskController',
            'title' => 'Liste des tâches',
            'tasks' => $emi->getRepository(Task::class)->findAll(),
        ]);
    }



    #[Route('/tasks/create', name: 'app_task_create')]
    public function createAction(EntityManagerInterface $emi, Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour créer une tâche.');

            return $this->redirectToRoute('app_login');
        }

        $task = new Task();

        $form = $this->createForm(TaskType::class, $task, [
            'action' => $this->generateUrl('app_task_create')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $task->getAuthor() === null ? $task->setAuthor($this->getUser()) : null;

            $emi->persist($task);
            $emi->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('app_tasks_list');
        }

        return $this->render('task/create.html.twig', [
            'controller_name' => 'TaskController',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/tasks/{id}/edit', name: 'app_task_edit')]
    public function editTaskAction(Task $task, EntityManagerInterface $emi, Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour modifier une tâche.');

            return $this->redirectToRoute('app_login');
        } else if ($this->getUser() !== $task->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier une tâche qui ne vous appartient pas.');

            return $this->redirectToRoute('app_tasks_list');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $task->setAuthor($emi->getRepository(User::class)->findOneBy(['username' => $form->get('author')->getData()]));
            $emi->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('app_tasks_list');
        }

        return $this->render('task/edit.html.twig', [
            'controller_name' => 'TaskController',
            'form' => $form->createView(),
            'task' => $task
        ]);
    }



    #[Route('/tasks/{id}/toggle', name: 'app_task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour clôturer une tâche.');

            return $this->redirectToRoute('app_login');
        }
        $task->setIsDone(!$task->isIsDone());
        $emi->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme terminée.', $task->getTitle()));

        return $this->redirectToRoute('app_tasks_list');
    }



    #[Route('/tasks/{id}/delete', name: 'app_task_delete')]
    public function deleteTaskAction(Task $task, EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour supprimer une tâche.');

            return $this->redirectToRoute('app_login');
        } else if ($this->getUser() !== $task->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer une tâche qui ne vous appartient pas.');

            return $this->redirectToRoute('app_tasks_list');
        }

        $emi->remove($task);
        $emi->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('app_tasks_list');
    }



    #[Route('/tasks/done', name: 'app_tasks_done')]
    public function doneTasksAction(EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté accéder aux tâches clôturées.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('task/list.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $emi->getRepository(Task::class)->findBy(['isDone' => true]),
            'title' => 'Tâches clôturées'
        ]);
    }



    #[Route('/tasks/user/{id}', name: 'app_tasks_user')]
    public function userTasksAction(User $user, EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder votre liste des tâches.');

            return $this->redirectToRoute('app_login');
        } else if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous ne pouvez pas accéder à la liste des tâches d\'un autre utilisateur.');

            return $this->redirectToRoute('app_tasks_list');
        }

        return $this->render('task/list.html.twig', [
            'controller_name' => 'TaskController',
            'title' => 'Tâches de ' . $user->getUsername(),
            'tasks' => $emi->getRepository(Task::class)->findBy(['author' => $user]),
        ]);
    }
}