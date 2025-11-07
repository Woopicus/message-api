<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateMessageDTO
{
    #[Assert\NotBlank]
    public string $type = 'info';

    #[Assert\NotBlank]
    public string $subject = '';

    #[Assert\NotBlank]
    public string $content = '';

    #[Assert\NotNull]
    public \DateTimeImmutable $date;

    #[Assert\NotBlank]
    public string $senderName = '';

    public ?\DateTimeImmutable $processedAt = null;
    public ?string $handler = null;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }
}
