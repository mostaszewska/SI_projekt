<?php
/**
 * Users controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserdataType;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UsersController.
 *
 * @Route("/user")
 *
 */
class UserController extends AbstractController
{
    /**
     * User service.
     *
     * @var \App\Service\UserService
     */
    private $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP request
     * @param UserRepository $userRepository User repository
     * @param PaginatorInterface $paginator Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="user_index",
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userRepository->queryAll(),
            $request->query->getInt('page', 1),
            UserRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'user/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="user_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function show(User $user): Response
    {
        return $this->render(
            'user/show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param User $user
     * @param UserRepository $userRepository
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_edit",
     * )
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $log = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN') ) {
            $form = $this->createForm(UserdataType::class, $user, ['method' => 'PUT']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newPassword = $form->get('newPassword')->getData();
                $userRepository->save($user, $newPassword);

                $this->addFlash('success', 'message_updated_successfully');

                return $this->redirectToRoute('task_index');
            }

            return $this->render(
                'user/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user,
                ]
            );
        }
        else {
            {
                $form = $this->createForm(UserdataType::class, $log, ['method' => 'PUT']);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $newPassword = $form->get('newPassword')->getData();
                    $userRepository->save($log, $newPassword);

                    $this->addFlash('success', 'message_updated_successfully');

                    return $this->redirectToRoute('task_index');
                }

                return $this->render(
                    'user/edit.html.twig',
                    [
                        'form' => $form->createView(),
                        'user' => $log,
                    ]
                );


            }
        }
    }

}