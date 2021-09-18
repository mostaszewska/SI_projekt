<?php
/**
 * Index controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController.
 *
 * @Route("/")
 */
class IndexController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="index",
     * )
     *
     */
    public function index(): Response
    {
        return $this->render(
            'index.html.twig',
        );
    }
}
