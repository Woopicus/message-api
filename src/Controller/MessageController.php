<?php

namespace App\Controller;

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

        return $this->json([
            'data' => $messages,
        ]);
    }

    #[Route('/api/messages/{messageId}', methods: ['GET'])]
    public function getMessage(int $messageId): JsonResponse
    {
        $message = $this->messageService->getMessage($messageId);

        return $this->json([
            'data' => $message,
        ]);
    }

    #[Route('/api/messages', methods: ['POST'])]
    public function createMessage(Request $request): JsonResponse
    {
        $type = $request->getPayload()->get('type');
        $subject = $request->getPayload()->get('subject');
        $content = $request->getPayload()->get('content');
        $date = new \DateTimeImmutable($request->getPayload()->get('date'));
        $senderName = $request->getPayload()->get('senderName');
        $processedAt = $request->getPayload()->get('processedAt')
            ? new \DateTimeImmutable($request->getPayload()->get('processedAt'))
            : null;
        $handler = $request->getPayload()->get('handler');

        $message = $this->messageService->createMessage(
            $type,
            $subject,
            $content,
            $date,
            $senderName,
            $processedAt,
            $handler
        );

        return $this->json([
            'data' => $message,
        ], 201);
    }

    #[Route('/api/messages/{messageId}', methods: ['PUT'])]
    public function updateMessage(Request $request, int $messageId): JsonResponse
    {
        $type = $request->getPayload()->get('type');
        $subject = $request->getPayload()->get('subject');
        $content = $request->getPayload()->get('content');
        $date = $request->getPayload()->get('date')
            ? new \DateTimeImmutable($request->getPayload()->get('date'))
            : null;
        $senderName = $request->getPayload()->get('senderName');
        $processedAt = $request->getPayload()->get('processedAt')
            ? new \DateTimeImmutable($request->getPayload()->get('processedAt'))
            : null;
        $handler = $request->getPayload()->get('handler');

        $message = $this->messageService->updateMessage(
            $messageId,
            $type,
            $subject,
            $content,
            $date,
            $senderName,
            $processedAt,
            $handler
        );

        return $this->json([
            'data' => $message,
        ]);
    }

    #[Route('/api/messages/{messageId}', methods: ['DELETE'])]
    public function removeMessage(int $messageId): JsonResponse
    {
        $this->messageService->removeMessage($messageId);

        return $this->json(null);
    }
}
