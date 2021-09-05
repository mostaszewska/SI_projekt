<?php
/**
 * Task controller.
 */

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\AnswerRepository;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController.
 *
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * Task service.
     *
     * @var \App\Service\TaskService
     */
    private $taskService;


    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\TaskRepository            $taskRepository Task repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator      Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="task_index",
     * )
     */
    public function index(Request $request, TaskRepository $taskRepository, PaginatorInterface $paginator): Response
    {
        $filters = [];
        $categoryId = $request->query->getInt('filters_category_id');
        $tagId = $request->query->getInt('filters_tag_id');

        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }

        if ($tagId) {
            $filters['tag_id'] = $tagId;
        }

        $qb = $taskRepository->queryAll($filters);

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'task/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param \App\Entity\Task $task Task entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="task_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function show(Request $request, Task $task, PaginatorInterface $paginator, AnswerRepository $answerRepository): Response
    {
        $pagination = $paginator->paginate(
            $answerRepository->queryByTaskId($task->getId()),
            $request->query->getInt('page', 1),
            AnswerRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'task/show.html.twig',
            ['task' => $task, 'pagination' => $pagination]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\TaskRepository            $taskRepository Task repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="task_create",
     * )
     */
    public function create(Request $request, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());
            $taskRepository->save($task);
            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Task                          $task           Task entity
     * @param \App\Repository\TaskRepository            $taskRepository Task repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="task_edit",
     * )
     *
     * @IsGranted(
     *     "EDIT",
     *     subject="task",
     * )
     */
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Task                          $task           Task entity
     * @param \App\Repository\TaskRepository            $taskRepository Task repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="task_delete",
     * )
     *
     * @IsGranted(
     *     "DELETE",
     *     subject="task",
     * )
     */
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(FormType::class, $task, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->delete($task);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/delete.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }
}