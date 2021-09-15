<?php
/**
 * Answer service.
 */

namespace App\Service;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AnswerService.
 */
class AnswerService
{
    /**
     * Answer repository.
     *
     * @var AnswerRepository
     */
    private AnswerRepository $answerRepository;

    /**
     * Paginator.
     *
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * Task service.
     *
     * @var \App\Service\TaskService
     */
    private $taskService;

    /**
     * Task service.
     *
     * @var \App\Repository\TaskRepository
     */
    private $taskRepository;

    /**
     * AnswerService constructor.
     *
     * @param AnswerRepository $answerRepository Answer repository
     * @param PaginatorInterface $paginator          Paginator
     * @param \App\Service\TaskService            $taskService Task service
     */
    public function __construct(AnswerRepository $answerRepository, PaginatorInterface $paginator,TaskService $taskService)
    {
        $this->answerRepository = $answerRepository;
        $this->paginator = $paginator;
        $this->taskService = $taskService;
    }

    /**
     * Create paginated list.
     *
     * @param int $taskId
     * @param int $page
     * @return PaginationInterface
     */
    public function createPaginatedList(int $taskId, int $page = 1)
    {
        $pagination = $this->paginator->paginate(
            $this->answerRepository->queryByTaskId($taskId),
            $page,
            AnswerRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $pagination;
    }

    /**
     * Save Answer.
     *
     * @param \App\Entity\Answer $answer Answer entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Answer $answer): void
    {
        $this->answerRepository->save($answer);
    }

    /**
     * DeleteAnswer.
     *
     * @param \App\Entity\Answer $answer Answer entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Answer $answer): void
    {
        $this->answerRepository->delete($answer);
    }
    /**
     * Prepare filters for the tasks list.
     *
     * @param array $filters Raw filters from request
     *
     * @return array Result array of filters
     */
    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];

        if (isset($filters['task_id']) && is_numeric($filters['task_id'])) {
            $task = $this->taskService->findOneById($filters['task_id']);
            if (null !== $task) {
                $resultFilters['task'] = $task;
            }
        }


        return $resultFilters;
    }
}