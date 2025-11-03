<?php

namespace App\Controller;

use App\DTO\UpdateMessageDTO;
use App\DTO\CreateMessageDTO;
use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageService $messageService,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/api/messages', methods: ['GET'])]
    public function getMessages(): JsonResponse
    {
        $messages = $this->messageService->getMessages();

        return $this->json(['data' => $messages]);
    }

    #[Route('/api/messages/{messageId}', methods: ['GET'])]
    public function getMessage(int $messageId): JsonResponse
    {
        $message = $this->messageService->getMessage($messageId);

        return $this->json(['data' => $message]);
    }

    #[Route('/api/messages', methods: ['POST'])]
    public function createMessage(#[RequestDto] CreateMessageDTO $dto): JsonResponse
    {
        $message = $this->messageService->createMessage($dto);
        return $this->json(['data' => $message], JsonResponse::Method_CREATED);
    }

    #[Route('/api/messages/{messageId}', methods: ['PUT'])]
    public function updateMessage(int $messageId, #[RequestDto] UpdateMessageDTO $dto): JsonResponse
    {
        $message = $this->messageService->updateMessage($messageId, $dto);
        return $this->json(['data' => $message], JsonResponse::Method_CREATED);
    }

    #[Route('/api/messages/{messageId}', methods: ['DELETE'])]
    public function removeMessage(int $messageId): JsonResponse
    {
        $this->messageService->removeMessage($messageId);

        return $this->json(null, JsonResponse::Method_CREATED);
    }
}
