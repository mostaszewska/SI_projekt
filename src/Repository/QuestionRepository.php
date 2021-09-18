<?php
/**
 * QuestionRepository repository.
 */

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * Class QuestionRepository.
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
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
     * QuestionRepository constructor.
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Save record.
     *
     * @param \App\Entity\Question $question Question entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Question $question): void
    {
        $this->_em->persist($question);
        $this->_em->flush($question);
    }

    /**
     * Delete record.
     *
     * @param \App\Entity\Question $question Question entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Question $question): void
    {
        $this->_em->remove($question);
        $this->_em->flush();
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(array $filters): QueryBuilder
    {
        $qb = $this->getOrCreateQueryBuilder()
            ->select(
                'partial question.{id, createdAt, updatedAt, title}',
                'partial category.{id, title}',
                'partial tags.{id, title}',
                'partial author.{id, email}'
            )
            ->innerJoin('question.category', 'category')
            ->innerJoin('question.author', 'author')
            ->leftJoin('question.tags', 'tags')
            ->orderBy('question.updatedAt', 'DESC');

        if (array_key_exists('category_id', $filters) && $filters['category_id'] > 0) {
            $qb->andWhere('category.id = :category_id')
                ->setParameter('category_id', $filters['category_id']);
        }

        if (array_key_exists('tag_id', $filters) && $filters['tag_id'] > 0) {
            $qb->andWhere('tags.id = :tag_id')
                ->setParameter('tag_id', $filters['tag_id']);
        }

        return $qb;
    }

    /**
     * Query questions by author.
     *
     * @param \App\Entity\User $user User entity
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryByAuthor(User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('question.author = :author')
            ->setParameter('author', $user);

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
        return $queryBuilder ?? $this->createQueryBuilder('question');
    }
}
