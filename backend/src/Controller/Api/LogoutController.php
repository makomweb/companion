<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/logout', name: 'app_logout', methods: ['POST'])]
class LogoutController extends AbstractController
{
    public function __invoke(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        return $this->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
