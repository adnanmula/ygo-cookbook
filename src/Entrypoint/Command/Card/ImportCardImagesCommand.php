<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Import card translations from ygorganization api')
            ->addOption('ids', null, InputOption::VALUE_REQUIRED, 'Ids to import, comma separated');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ids = $input->getOption('ids') ?? null;

        $cards = $this->cards($ids);

        $progressBar = new ProgressBar($output, \count($cards));

        $progressBar->start();

        foreach ($cards as $card) {
            $this->importImage($output, 'cards', $card);
            $this->importImage($output, 'cards_small', $card);
            $this->importImage($output, 'cards_cropped', $card);

            $progressBar->advance();
        }

        $progressBar->finish();

        return self::SUCCESS;
    }

    private function cards(?string $ids): array
    {
        $filters = [];

        if (null !== $ids) {
            $filters[] = new Filters(
                FilterType::AND,
                FilterType::AND,
                new Filter(
                    new FilterField('id'),
                    new StringArrayFilterValue(...\explode(',', $ids)),
                    FilterOperator::IN,
                ),
            );
        }

        return $this->cardRepository->search(new Criteria(null, null, null, ...$filters));
    }

    private function importImage(OutputInterface $output, string $type, Card $card): void
    {
        if (\file_exists('public/assets/ygo/' . $type . '/' . $card->id->value() . '.jpg')) {
            if ($output->isVerbose()) {
                $output->writeln($card->id . ' - ' . $card->name->get() . ' | ' . $type . ' | already imported');
            }

            return;
        }

        try {
            $image = $this->ygoproImagesClient
                ->request(Request::METHOD_GET, '/images/' . $type . '/' . $card->references->ygoProId . '.jpg')
                ->getContent();

            \file_put_contents('public/assets/ygo/' . $type . '/' . $card->id->value() . '.jpg', $image);
        } catch (\Throwable) {
        }
    }
}
