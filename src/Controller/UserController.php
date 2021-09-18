<?php
/**
 * Users controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserdataType;
use App\Service\UserService;
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

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP request
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
    public function index(Request $request): Response
    {
        $pagination = $this->userService->createPaginatedList($request->query->getInt('page', 1));

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
     * @param User    $user
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_edit",
     * )
     */
    public function edit(Request $request, User $user): Response
    {
        $log = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(UserdataType::class, $user, ['method' => 'PUT']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newPassword = $form->get('newPassword')->getData();
                $this->userService->save($user, $newPassword);

                $this->addFlash('success', 'message_updated_successfully');

                return $this->redirectToRoute('question_index');
            }

            return $this->render(
                'user/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user,
                ]
            );
        } else {
            {
                $form = $this->createForm(UserdataType::class, $log, ['method' => 'PUT']);
                $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newPassword = $form->get('newPassword')->getData();
                $this->userService->save($log, $newPassword);

                $this->addFlash('success', 'message_updated_successfully');

                return $this->redirectToRoute('question_index');
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
