<?php

declare(strict_types=1);


namespace App\DTO;

class CreateMessageDTO
{
    public string $type;
    public string $subject;
    public string $content;
    public \DateTimeImmutable $date;
    public string $senderName;
    public ?\DateTimeImmutable $processedAt = null;
    public ?string $handler = null;
}
