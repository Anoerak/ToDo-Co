<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_tasks_list')]
    public function listAction(EntityManagerInterface $emi): Response
    {
        return $this->render('task/list.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $emi->getRepository(Task::class)->findAll(),
        ]);
    }



    #[Route('/tasks/create', name: 'app_task_create')]
    public function createAction(EntityManagerInterface $emi, Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isValid) {
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



    #[Route('/tasks/{id}/toggle', name: 'app_task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $emi): Response
    {
        $task->setIsDone(!$task->isIsDone());
        $emi->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('app_tasks_list');
    }



    #[Route('/tasks/{id}/delete', name: 'app_task_delete')]
    public function deleteTaskAction(Task $task, EntityManagerInterface $emi): Response
    {
        $emi->remove($task);
        $emi->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('app_tasks_list');
    }
}