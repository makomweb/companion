<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\AI\Chat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chat', name: 'app_chat', methods: ['POST'])]
class ChatController extends AbstractController
{
    public function __construct(private readonly Chat $chat)
    {
    }

    public function __invoke(Request $request): StreamedResponse
    {
        /** @var array{message: string, thread_id: string} $data */
        $data = $request->toArray();

        try {
            return ChatResponse::create($this->chat, $data);
        } catch (\Throwable $ex) {
            return new StreamedResponse(['message' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
