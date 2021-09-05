<?php
/**
 * Answer controller.
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Service\AnswerService;
use App\Entity\Task;
use App\Form\AnswerType;
use App\Form\AnswerAnonimType;
use App\Repository\AnswerRepository;
use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AnswerController.
 *
 * @Route("/answer")
 */
class AnswerController extends AbstractController
{
    /**
     * Answer service.
     *
     * @var \App\Service\AnswerService
     */
    private $answerService;


    public function __construct(AnswerService $answerService)
    {
        $this->answerService = $answerService;
    }

    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Repository\AnswerRepository $answerRepository Answer repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="answer_index",
     * )
     */
    public function index(Request $request, AnswerRepository $answerRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $answerRepository->queryByTaskId($request->query->get('filters_task_id')),
            $request->query->getInt('page', 1),
            AnswerRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'answer/index.html.twig',
            ['pagination' => $pagination]
        );
    }


    /**
     * Show action.
     *
     * @param \App\Entity\Answer $answer Answer entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="answer_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function show(Answer $answer): Response
    {
        return $this->render(
            'answer/show.html.twig',
            ['answer' => $answer]
        );
    }

    /**
     * Answer action.
     *
     * @param Request $request HTTP request
     * @return Response HTTP response
     *
     * @Route(
     *     "/add",
     *     methods={"GET", "POST"},
     *     name="answer_add",
     * )
     */
    public function add(Request $request): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerAnonimType::class, $answer);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_USER') === false) {
            if ($form->isSubmitted() && $form->isValid()) {
                // $answer->setAuthorName($this-> getAuthorName);
                $answer->setcreatedAt(new \DateTime());
                $answer->setupdatedAt(new \DateTime());
                $this->answerService->save($answer);
                $this->addFlash('success', 'message_created_successfully');

                return $this->redirectToRoute('task_index');

            }

        }
        return $this->render(
            'answer/add.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Repository\AnswerRepository $answerRepository Answer repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="answer_create",
     * )
     */
    public function create(Request $request, AnswerRepository $answerRepository): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setAuthor($this->getUser());
            $answerRepository->save($answer);
            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'answer/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Answer $answer Answer entity
     * @param \App\Repository\AnswerRepository $answerRepository Answer repository
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
     *     name="answer_edit",
     * )
     *
     * @IsGranted(
     *     "EDIT",
     *     subject="answer",
     * )
     */
    public function edit(Request $request, Answer $answer, AnswerRepository $answerRepository): Response
    {
        $form = $this->createForm(AnswerType::class, $answer, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answerRepository->save($answer);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'answer/edit.html.twig',
            [
                'form' => $form->createView(),
                'answer' => $answer,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Answer $answer Answer entity
     * @param \App\Repository\AnswerRepository $answerRepository Answer repository
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
     *     name="answer_delete",
     * )
     *
     * @IsGranted(
     *     "DELETE",
     *     subject="answer",
     * )
     */
    public function delete(Request $request, Answer $answer, AnswerRepository $answerRepository): Response
    {
        $form = $this->createForm(FormType::class, $answer, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $answerRepository->delete($answer);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'answer/delete.html.twig',
            [
                'form' => $form->createView(),
                'answer' => $answer,
            ]
        );
    }



}


