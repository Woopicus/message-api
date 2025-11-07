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
                'handler' => null
            ])
        );

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertSame('info', $data['data']['type']);
    }
}
