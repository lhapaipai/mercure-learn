<?php

namespace App\Controller;

use App\Service\MyPublisher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_welcome')]
    public function index(): Response
    {
        return $this->render('default/welcome.html.twig');
    }

    #[Route('/publish', name: 'app_publish')]
    public function publish(Request $request, HubInterface $hub, Discovery $discovery, Authorization $authorization, MyPublisher $myPublisher): Response
    {
        $authorization->setCookie($request, ['http://localhost/my-private-topic']);
        $update = new Update(
            'http://localhost/my-private-topic',
            json_encode([
                'status' => 'OutOfStock',
            ]),
            true
        );
        $hub->publish($update);

        return $this->render('default/published.html.twig');
    }

    #[Route('/discover', name: 'app_discover')]
    public function discover(Request $request, Discovery $discovery): JsonResponse
    {
        $discovery->addLink($request);

        return $this->json([
            '@id' => '/books/1',
            'availability' => 'https://schema.org/InStock',
        ]);
    }
}
