<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Criteria\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportCardImagesCommand extends Command
{
    public const NAME = 'import:cards:images';

    public function __construct(
        private readonly HttpClientInterface $ygoproImagesClient,
        private readonly CardRepository $cardRepository,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Import card translations from ygorganization api');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cards = $this->cardRepository->search(new Criteria(null, null, null));

        $progressBar = new ProgressBar($output, \count($cards));

        $progressBar->start();

        foreach ($cards as $card) {
            $this->importImage('cards', $card);
            $this->importImage('cards_small', $card);
            $this->importImage('cards_cropped', $card);

            $progressBar->advance();
        }

        $progressBar->finish();

        return self::SUCCESS;
    }

    public function importImage(string $type, Card $card): void
    {
        try {
            $image = $this->ygoproImagesClient
                ->request(Request::METHOD_GET, '/images/' . $type . '/' . $card->references->ygoProId . '.jpg')
                ->getContent();

            \file_put_contents('public/assets/ygo/' . $type . '/' . $card->id->value() . '.jpg', $image);
        } catch (\Throwable) {
        }
    }
}
