<?php
/**
 * Question controller.
 */

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Service\AnswerService;
use App\Service\QuestionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionController.
 *
 * @Route("/questions")
 */
class QuestionController extends AbstractController
{
    /**
     * Question service.
     *
     * @var \App\Service\QuestionService
     */
    private $questionService;

    /**
     * Answer service.
     *
     * @var \App\Service\AnswerService
     */
    private $answerService;

    /**
     * @param \App\Service\QuestionService $questionService
     * @param \App\Service\AnswerService   $answerService
     */
    public function __construct(QuestionService $questionService, AnswerService $answerService)
    {
        $this->questionService = $questionService;
        $this->answerService = $answerService;
    }

    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="question_index",
     * )
     */
    public function index(Request $request): Response
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

        $pagination = $this->questionService->createPaginatedList(
            $request->query->getInt('page', 1),
            $filters
        );

        return $this->render(
            'question/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\Question                      $question Question entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="question_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function show(Request $request, Question $question): Response
    {
        $pagination = $this->answerService->createPaginatedList($question->getId(), $request->query->getInt('page', 1));

        return $this->render(
            'question/show.html.twig',
            ['question' => $question, 'pagination' => $pagination]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="question_create",
     * )
     */
    public function create(Request $request): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setAuthor($this->getUser());
            $this->questionService->save($question);

            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\Question                      $question Question entity
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
     *     name="question_edit",
     * )
     *
     * @IsGranted(
     *     "EDIT",
     *     subject="question",
     * )
     */
    public function edit(Request $request, Question $question): Response
    {
        $form = $this->createForm(QuestionType::class, $question, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->save($question);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/edit.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\Question                      $question Question entity
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
     *     name="question_delete",
     * )
     *
     * @IsGranted(
     *     "DELETE",
     *     subject="question",
     * )
     */
    public function delete(Request $request, Question $question): Response
    {
        $form = $this->createForm(FormType::class, $question, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->delete($question);

            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/delete.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
            ]
        );
    }
}
