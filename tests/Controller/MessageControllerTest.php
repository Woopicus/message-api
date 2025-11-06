<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{
    public function testCreateMessage(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test bericht',
                'content' => 'Dit is een test',
                'author' => 'Tester'
            ])
        );

        $this->assertResponseStatusCodeSame(201);
    }
}
