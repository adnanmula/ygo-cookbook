<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportCardsCommand extends Command
{
    public const NAME = 'card:import';

    public function __construct(
        private readonly HttpClientInterface $ygoproClient,
        private readonly CardRepository $cardRepository,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->ygoproClient->request(Request::METHOD_GET, '/api/v7/cardinfo.php?format=Speed Duel&misc=yes');
        $response = $response->toArray();

        foreach ($response['data'] as $card) {
            if (\in_array($card['frameType'], ['link', 'token'], true)) {
                continue;
            }

            $this->cardRepository->save(Card::fromYgoProArray($card));
        }

        return self::SUCCESS;
    }
}