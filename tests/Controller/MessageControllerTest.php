<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DTO\CreateMessageDTO;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{
    public function testCreateMessage(): void
    {
        $client = static::createClient();

        $dto = new CreateMessageDTO();
        $dto->type = 'info';
        $dto->subject = 'Testbericht';
        $dto->content = 'Dit is een test';
        $dto->date = new \DateTimeImmutable('2025-11-06 10:00:00');
        $dto->senderName = 'Tester';
        $dto->processedAt = null;
        $dto->handler = null;

        $client->request(
            'POST',
            '/api/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => $dto->type,
                'subject' => $dto->subject,
                'content' => $dto->content,
                'date' => $dto->date->format('c'),
                'senderName' => $dto->senderName,
                'processedAt' => null,
                'handler' => null,
            ])
        );

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertSame('info', $data['data']['type']);
    }

    public function testUpdateMessage(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'info',
                'subject' => 'Min',
                'content' => 'Cat',
                'date' => '2025-11-10T10:00:00+00:00',
                'senderName' => 'Tester',
                'processedAt' => null,
                'handler' => null,
            ])
        );

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $messageId = $responseData['data']['id'] ?? null;
        $this->assertNotNull($messageId, 'Er moet een message ID terugkomen.');

        $client->request(
            'PUT',
            "/api/messages/{$messageId}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'warning',
                'subject' => 'Update message',
                'content' => 'New message',
                'date' => '2025-11-11T10:00:00+00:00',
                'senderName' => 'Senior Tester',
                'processedAt' => null,
                'handler' => 'HandlerX',
            ])
        );

        $this->assertResponseStatusCodeSame(200);

        $updateResponse = json_decode($client->getResponse()->getContent(), true);
        $updatedMessage = $updateResponse['data'];

        $this->assertSame('warning', $updatedMessage['type']);
        $this->assertSame('Update message', $updatedMessage['subject']);
        $this->assertSame('New message', $updatedMessage['content']);
        $this->assertSame('Senior Tester', $updatedMessage['senderName']);
    }
}
