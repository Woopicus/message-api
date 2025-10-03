<?php

namespace App\Command;

use App\Service\MessageService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:get-messages')]
class GetMessagesCommand extends Command
{
    public function __construct(
        private readonly MessageService $messageService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        dump(
            $this->messageService->getMessages()
        );

        return Command::SUCCESS;
    }
}
