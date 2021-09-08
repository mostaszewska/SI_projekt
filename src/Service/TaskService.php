<?php
/**
 * Task service.
 */

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskService.
 */
class TaskService
{
    /**
     * Task repository.
     *
     * @var \App\Repository\TaskRepository
     */
    private $taskRepository;


    /**
     * Paginator.
     *
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private $paginator;

    /**
     * Category service.
     *
     * @var \App\Service\CategoryService
     */
    private $categoryService;



    /**
     * Tag service.
     *
     * @var \App\Repository\TagRepository
     */
    private $tagRepository;
    /**
     * Category service.
     *
     * @var \App\Repository\CategoryRepository
     */
    private $categoryRepository;

    /**
     * Tag service.
     *
     * @var \App\Service\TagService
     */
    private $tagService;

    /**
     * CategoryService constructor.
     *
     * @param \App\Repository\TaskRepository      $taskRepository Task repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator          Paginator
     * @param \App\Service\CategoryService            $categoryService Category service
     * @param \App\Service\TagService                 $tagService      Tag service
     */
    public function __construct(TaskRepository $taskRepository, PaginatorInterface $paginator, CategoryService $categoryService, TagService $tagService)
    {
        $this->taskRepository = $taskRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;

    }
    /**
     * Create paginated list.
     *
     *
     * @param int                                                   $page Page number
     * @param UserInterface $user    User entity
     * @param array                                               $filters Filters array
     *
     * @return PaginationInterface Paginated list
     */
    public function getByCategoryId(int $page, UserInterface $user, String $categoryId): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->taskRepository->queryByAuthor($user, $filters),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Create paginated list.
     *
     *
     * @param int $page Page number
     * @param array $filters Filters array
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedListNotAuthor(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->taskRepository->queryAll($filters),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }
    /**
     * Save category.
     *
     * @param Task $task Task entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Task $task): void
    {
        $this->taskRepository->save($task);
    }

    /**
     * Delete category.
     *
     * @param Task $task Task entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Task $task): void
    {
        $this->taskRepository->delete($task);
    }
    /**
     * Find Task by Id.
     *
     * @param int $id Task Id
     *
     * @return Task|null Task entity
     */
    public function findOneById(int $id): ?Task
    {
        return $this->taskRepository->findOneById($id);
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
        if (isset($filters['category_id']) && is_numeric($filters['category_id'])) {
            $category = $this->categoryService->findOneById(
                $filters['category_id']
            );
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        if (isset($filters['tag_id']) && is_numeric($filters['tag_id'])) {
            $tag = $this->tagService->findOneById($filters['tag_id']);
            if (null !== $tag) {
                $resultFilters['tag'] = $tag;
            }
        }
        return $resultFilters;
    }


}