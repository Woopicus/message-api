<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MessageServiceTest extends TestCase
{
    public function testGetMessageReturnsMessage(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(MessageRepository::class);
        $service = new MessageService($entityManager, $repo);

        $fakeMessage = new Message();
        $fakeMessage->setType('info');
        $fakeMessage->setSubject('Mock Subject');

        $repo
            ->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn($fakeMessage);

        $result = $service->getMessage(10);

        $this->assertSame($fakeMessage, $result);
        $this->assertSame('Mock Subject', $result->getSubject());
    }

    public function testGetMessageReturnsNullWhenNotFound(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(MessageRepository::class);
        $service = new MessageService($entityManager, $repo);

        $repo
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $result = $service->getMessage(999);

        $this->assertNull($result);
    }

    public function testRemoveMessageDeletesEntity(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(MessageRepository::class);
        $service = new MessageService($entityManager, $repo);

        $fakeMessage = new Message();

        $repo
            ->expects($this->once())
            ->method('find')
            ->with(5)
            ->willReturn($fakeMessage);

        $entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($fakeMessage);

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $service->removeMessage(5);
    }

    public function testRemoveMessageDoesNothingWhenNotFound(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(MessageRepository::class);
        $service = new MessageService($entityManager, $repo);

        $repo
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $entityManager->expects($this->never())->method('remove');
        $entityManager->expects($this->never())->method('flush');

        $service->removeMessage(999);
    }

    public function testCreateMessageCreatesEntity(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(MessageRepository::class);
        $service = new MessageService($entityManager, $repo);

        $dto = new \App\DTO\CreateMessageDTO();
        $dto->type = 'info';
        $dto->subject = 'Test Subject';
        $dto->content = 'Test Content';
        $dto->date = new \DateTimeImmutable('2025-01-01');
        $dto->senderName = 'Tester';

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Message $message) use ($dto) {
                return $message->getType() === $dto->type &&
                    $message->getSubject() === $dto->subject &&
                    $message->getContent() === $dto->content &&
                    $message->getSenderName() === $dto->senderName;
            }));

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $message = $service->createMessage($dto);

        $this->assertSame('info', $message->getType());
        $this->assertSame('Test Subject', $message->getSubject());
    }

    public function testUpdateMessageSimple(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(MessageRepository::class);
        $service = new MessageService($entityManager, $repo);

        $existing = new Message();

        $dto = new \App\DTO\UpdateMessageDTO();
        $dto->subject = 'Updated';

        $repo
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existing);

        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $result = $service->updateMessage(1, $dto);

        $this->assertSame('Updated', $result->getSubject());
    }
}
