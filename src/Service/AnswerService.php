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
     * Question service.
     *
     * @var \App\Service\QuestionService
     */
    private $questionService;

    /**
     * AnswerService constructor.
     *
     * @param AnswerRepository             $answerRepository Answer repository
     * @param PaginatorInterface           $paginator        Paginator
     * @param \App\Service\QuestionService $questionService  Question service
     */
    public function __construct(AnswerRepository $answerRepository, PaginatorInterface $paginator, QuestionService $questionService)
    {
        $this->answerRepository = $answerRepository;
        $this->paginator = $paginator;
        $this->questionService = $questionService;
    }

    /**
     * Create paginated list.
     *
     * @param int $questionId
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function createPaginatedList(int $questionId, int $page = 1)
    {
        $pagination = $this->paginator->paginate(
            $this->answerRepository->queryByQuestionId($questionId),
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
     * Prepare filters for the questions list.
     *
     * @param array $filters Raw filters from request
     *
     * @return array Result array of filters
     */
    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];

        if (isset($filters['question_id']) && is_numeric($filters['question_id'])) {
            $question = $this->questionService->findOneById($filters['question_id']);
            if (null !== $question) {
                $resultFilters['question'] = $question;
            }
        }


        return $resultFilters;
    }
}
