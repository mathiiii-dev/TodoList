<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Handler\TaskHandler;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;
    private TaskHandler $handler;
    private PaginatorInterface $paginator;

    public function __construct(
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager,
        TaskHandler $handler,
        PaginatorInterface $paginator
    ) {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
        $this->handler = $handler;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(Request $request): Response
    {
        $tasks = $this->taskRepository->findAll();
        $data = $this->paginator->paginate($tasks, $request->query->getInt('page', 1), 6);

        return $this->render('task/list.html.twig', [
            'tasks' => $data,
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request): RedirectResponse|Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User */
            $user = $this->getUser();
            $this->handler->handleCreate($user, $task);

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit", requirements={"id"="\d+"})
     */
    public function editAction(Task $task, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle", requirements={"id"="\d+"})
     */
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        $this->handler->handleIsDone($task);

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete", requirements={"id"="\d+"})
     */
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        if ($task->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $this->handler->handleDelete($task);

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
