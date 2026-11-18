<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/health', name: 'app_health', methods: ['GET'])]
class HealthController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'version' => $this->getParameter('app.version'),
            'timestamp' => (new \DateTime())->format(\DateTime::ISO8601),
        ]);
    }
}
