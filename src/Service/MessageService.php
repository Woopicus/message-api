<?php

declare(strict_types=1);


namespace App\Service;

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

    public function createMessage(
        string $type,
        string $subject,
        string $content,
        \DateTimeImmutable $date,
        string $senderName,
        ?\DateTimeImmutable $processedAt,
        ?string $handler
    ): Message {
        $message = new Message();
        $message->setType($type);
        $message->setSubject($subject);
        $message->setContent($content);
        $message->setDate($date);
        $message->setSenderName($senderName);
        $message->setProcessedAt($processedAt);
        $message->setHandler($handler);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function updateMessage(
        int $messageId,
        string $type,
        string $subject,
        string $content,
        ?\DateTimeImmutable $date,
        string $senderName,
        ?\DateTimeImmutable $processedAt,
        ?string $handler
    ): ?Message {
        $message = $this->getMessage($messageId);

        if ($message) {
            $message->setType($type);
            $message->setSubject($subject);
            $message->setContent($content);

            if ($date) {
                $message->setDate($date);
            }

            $message->setSenderName($senderName);
            $message->setProcessedAt($processedAt);
            $message->setHandler($handler);

            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }

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
