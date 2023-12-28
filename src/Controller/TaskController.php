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

class TaskController extends AbstractController
{
    /**
     * Retrieves and displays a list of tasks.
     *
     * @param EntityManagerInterface $emi The entity manager interface.
     * @return Response The response object.
     */
    #[Route('/tasks', name: 'app_tasks_list', methods: ['GET'])]
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



    /**
     * Creates a new task.
     *
     * This method is responsible for creating a new task. It checks if the user is authenticated, 
     * and if not, it redirects them to the login page.
     * It creates a new instance of the Task entity and a form to handle the task creation. 
     * If the form is submitted and valid, the task is persisted to the database and a 
     * success flash message is displayed. Finally, the user is redirected to the task list page.
     *
     * @param EntityManagerInterface $emi The entity manager interface.
     * @param Request $request The request object.
     * @return Response The response object.
     */
    #[Route('/tasks/create', name: 'app_task_create', methods: ['GET', 'POST'])]
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



    /**
     * Edit a task.
     *
     * This method allows the user to edit a task. If the user is not logged in, 
     * they will be redirected to the login page.
     * If the user is not the author of the task and does not have the ROLE_ADMIN role, 
     * they will be redirected to the task list page.
     * If the form is submitted and valid, the task will be updated and the user will be 
     * redirected to the task list page.
     *
     * @param Task $task The task to be edited.
     * @param EntityManagerInterface $emi The entity manager interface.
     * @param Request $request The request object.
     * @return Response The response object.
     */
    #[Route('/tasks/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function editTaskAction(Task $task, EntityManagerInterface $emi, Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour modifier une tâche.');

            return $this->redirectToRoute('app_login');
        } elseif ($this->getUser() !== $task->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier une tâche qui ne vous appartient pas.');

            return $this->redirectToRoute('app_tasks_list');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $task->setAuthor($emi->getRepository(User::class)->findOneBy(
            //      ['username' => $form->get('author')->getData()]));
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



    /**
     * Toggle the status of a task.
     *
     * This method toggles the status of a task between "done" and "not done".
     * If the user is not logged in, they will be redirected to the login page.
     * After toggling the status, a success flash message is added and the user is redirected to the task list page.
     *
     * @param Task $task The task to toggle.
     * @param EntityManagerInterface $emi The entity manager interface.
     * @return Response The response object.
     */
    #[Route('/tasks/{id}/toggle', name: 'app_task_toggle', methods: ['GET', 'POST'])]
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



    /**
     * Deletes a task.
     *
     * This method is responsible for deleting a task from the database. 
     * It checks if the user is authenticated and has the necessary permissions to delete the task.
     * If the user is not authenticated, they are redirected to the login page with a flash message 
     * indicating that they need to be logged in to delete a task.
     * If the user is not the author of the task and does not have the ROLE_ADMIN role, 
     * they are redirected to the task list page with a flash message indicating that they 
     * cannot delete a task that does not belong to them.
     * If the user is authenticated and has the necessary permissions, 
     * the task is removed from the database and a success flash message is displayed.
     * Finally, the user is redirected to the task list page.
     *
     * @param Task $task The task to be deleted.
     * @param EntityManagerInterface $emi The entity manager interface used to interact with the database.
     * @return Response The response object.
     */
    #[Route('/tasks/{id}/delete', name: 'app_task_delete', methods: ['GET', 'DELETE'])]
    public function deleteTaskAction(Task $task, EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour supprimer une tâche.');

            return $this->redirectToRoute('app_login');
        } elseif ($this->getUser() !== $task->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer une tâche qui ne vous appartient pas.');

            return $this->redirectToRoute('app_tasks_list');
        }

        $emi->remove($task);
        $emi->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('app_tasks_list');
    }



    /**
     * Retrieves and displays the list of completed tasks.
     *
     * @param EntityManagerInterface $emi The entity manager interface.
     * @return Response The response object.
     *
     */
    #[Route('/tasks/done', name: 'app_tasks_done', methods: ['GET'])]
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



    /**
     * Retrieves and displays the tasks of a specific user.
     *
     * @param User $user The user whose tasks are to be displayed.
     * @param EntityManagerInterface $emi The entity manager interface.
     * @return Response The response object.
     */
    #[Route('/tasks/user/{id}', name: 'app_tasks_user', methods: ['GET'])]
    public function userTasksAction(User $user, EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder votre liste des tâches.');

            return $this->redirectToRoute('app_login');
        } elseif ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
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
