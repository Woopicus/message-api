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
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $repoMock = $this->createMock(MessageRepository::class);

        $service = new MessageService($entityManagerMock, $repoMock);

        $fakeMessage = new Message();
        $fakeMessage->setType('info');
        $fakeMessage->setSubject('Mock Subject');

        $repoMock
            ->method('find')
            ->with(10)
            ->willReturn($fakeMessage);

        $result = $service->getMessage(10);

        $this->assertSame($fakeMessage, $result);
        $this->assertEquals('Mock Subject', $result->getSubject());
    }

    public function testGetMessageReturnsNullWhenNotFound(): void
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $repoMock = $this->createMock(MessageRepository::class);

        $service = new MessageService($entityManagerMock, $repoMock);

        $repoMock
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

        $repo->expects($this->once())
            ->method('find')
            ->with(5)
            ->willReturn($fakeMessage);

        $entityManager->expects($this->once())
            ->method('remove')
            ->with($fakeMessage);

        $entityManager->expects($this->once())
            ->method('flush');

        $service->removeMessage(5);
    }

    public function testRemoveMessageDoesNothingWhenNotFound(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(MessageRepository::class);

        $service = new MessageService($entityManager, $repo);

        $repo->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $entityManager->expects($this->never())
            ->method('remove');

        $entityManager->expects($this->never())
            ->method('flush');

        $service->removeMessage(999);
    }
}
