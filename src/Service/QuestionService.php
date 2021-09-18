<?php
/**
 * QuestionService.
 */

namespace App\Service;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class QuestionService.
 */
class QuestionService
{
    /**
     * Question repository.
     *
     * @var \App\Repository\QuestionRepository
     */
    private $questionRepository;

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
     * @param \App\Repository\QuestionRepository      $questionRepository Question repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator          Paginator
     * @param \App\Service\CategoryService            $categoryService    Category service
     * @param \App\Service\TagService                 $tagService         Tag service
     */
    public function __construct(QuestionRepository $questionRepository, PaginatorInterface $paginator, CategoryService $categoryService, TagService $tagService)
    {
        $this->questionRepository = $questionRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
    }

    /**
     * Create paginated list.
     *
     * @param int   $page    Page number
     * @param array $filters Filters array
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->questionRepository->queryAll($filters),
            $page,
            QuestionRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }
    /**
     * Save category.
     *
     * @param Question $question Question entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Question $question): void
    {
        $this->questionRepository->save($question);
    }

    /**
     * Delete category.
     *
     * @param Question $question Question entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Question $question): void
    {
        $this->questionRepository->delete($question);
    }
    /**
     * Find Question by Id.
     *
     * @param int $id Question Id
     *
     * @return Question|null Question entity
     */
    public function findOneById(int $id): ?Question
    {
        return $this->questionRepository->findOneById($id);
    }
    /**
     * Prepare filters for the questions list.
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
