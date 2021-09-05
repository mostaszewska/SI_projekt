<?php
/**
 * Answer repository.
 */
namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * AnswerRepository constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * Query all records.
     *
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(array $filters = []): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('answer.createdAt', 'DESC');
//        $queryBuilder = $this->getOrCreateQueryBuilder()
//            ->select(
//                'partial answer.{id, createdAt, updatedAt, text, favourite}',
//                'partial tasks.{id, createdAt, updatedAt, text}',
//
//            )
//            ->join('answer.tasks', 'tasks')
//            -> orderBy('answer.favourite',  'DESC');
//        $queryBuilder = $this->applyFiltersToList($queryBuilder, $filters);
//
//        return $queryBuilder;
    }
    /**
     * Save record.
     *
     * @param Answer $answer Category entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Answer $answer): void
    {
        $this->_em->persist($answer);
        $this->_em->flush();
    }
//    /**
//     * Delete record.
//     *
//     * @param \App\Entity\Answer $answer Answer entity
//     *
//     * @throws \Doctrine\ORM\ORMException
//     * @throws \Doctrine\ORM\OptimisticLockException
//     */
//    public function delete(Answer $answer): void
//    {
//        $this->_em->remove($answer);
//        $this->_em->flush();
//    }

    /**
     * Query tasks by author.
     *
     * @param \App\Entity\User $user User entity
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryByAuthor(User $user, array $filters = []): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder->andWhere('answer.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }
    /**
     * Query tasks by author.
     *
     * @param \App\Entity\User $user User entity
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryByTaskId(string $task_id, array $filters = []): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder->andWhere('answer.tasks = :task_id')
            ->setParameter('task_id', $task_id);

        return $queryBuilder;
    }
    /**
     * Get or create new query builder.
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('answer');

    }
    /**
     * Apply filters to paginated list.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param array                      $filters      Filters array
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, array $filters = []): QueryBuilder
    {
        if (isset($filters['task']) && $filters['task'] instanceof Task) {
            $queryBuilder->andWhere('tasks = :task')
                ->setParameter('task', $filters['task']);
        }


        return $queryBuilder;
    }


    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
