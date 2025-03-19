<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CancelAppointment;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cancel-appointment',
    description: 'Cancel a medical appointment',
)]
class CancelAppointmentCommand extends Command
{
    public function __construct(
        private readonly CancelAppointment $cancelMedicalAppointment,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('reference', InputArgument::REQUIRED, 'The appointment reference number')
            ->addOption('reason', 'r', InputOption::VALUE_REQUIRED, 'The cancellation reason')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $reference = $input->getArgument('reference');
        \assert(\is_string($reference));

        $reason = $input->getOption('reason');
        \assert(\is_string($reason) || $reason === null);

        $this->cancelMedicalAppointment->cancelByReferenceNumber($reference, $reason);

        $io->success(\sprintf('Appointment %s has been cancelled successfully.', $reference));

        return Command::SUCCESS;
    }
}
