<?php
/**
 * Registration controller.
 */
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
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
     * Registration.
     *
     * @param Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Form\Exception\LogicException
     *
     * @Route("/register", methods={"GET", "POST"}, name="user_registration")
     * )
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->register($user);

            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}
