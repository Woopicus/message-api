<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreateMessageDTO;
use App\DTO\UpdateMessageDTO;
use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class MessageService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository $messageRepository,
    ) {}

    public function getMessages(): array
    {
        return $this->messageRepository->findAll();
    }

    public function getMessage(int $messageId): ?Message
    {
        return $this->messageRepository->find($messageId);
    }

    public function createMessage(CreateMessageDTO $dto): Message
    {
        $message = new Message();
        $message->setType($dto->type);
        $message->setSubject($dto->subject);
        $message->setContent($dto->content);
        $message->setDate($dto->date ?? new \DateTimeImmutable());
        $message->setSenderName($dto->senderName);
        $message->setProcessedAt($dto->processedAt);
        $message->setHandler($dto->handler);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function updateMessage(int $messageId, UpdateMessageDTO $dto): ?Message
    {
        $message = $this->getMessage($messageId);
        if (!$message) {
            return null;
        }

        $message->setType($dto->type);
        $message->setSubject($dto->subject);
        $message->setContent($dto->content);
        if ($dto->date) {
            $message->setDate($dto->date);
        }
        $message->setSenderName($dto->senderName);
        $message->setProcessedAt($dto->processedAt);
        $message->setHandler($dto->handler);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function removeMessage(int $messageId): void
    {
        $message = $this->getMessage($messageId);
        if ($message) {
            $this->entityManager->remove($message);
            $this->entityManager->flush();
        }
    }
}
