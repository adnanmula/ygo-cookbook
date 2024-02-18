<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\LocalizedString;
use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\Ygo\CardTranslationDbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\ArrayElementFilterField;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportCards2Command extends Command
{
    public const NAME = 'import:cards';

    private InputInterface $input;
    private OutputInterface $output;

    public function __construct(
        private readonly CardRepository $cardRepository,
        private readonly CardTranslationDbalRepository $cardTranslationRepository,
        private readonly HttpClientInterface $ygoproClient,
        private readonly HttpClientInterface $ygoproImagesClient,
        private readonly HttpClientInterface $ygorganizationClient,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Import cards')
            ->addOption('all', null, InputOption::VALUE_NONE, '')
            ->addOption('ygopro_ids', 'y', InputOption::VALUE_REQUIRED, '')
            ->addOption('konami_ids', 'k', InputOption::VALUE_REQUIRED, '')
            ->addOption('with_translations', 't', InputOption::VALUE_NONE, 'Ygopro id to search, comma separated')
            ->addOption('with_images', 'i', InputOption::VALUE_NONE, 'Import images');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;

        parent::initialize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $all = $input->getOption('all');
        $ygoproIds = $input->getOption('ygopro_ids');
        $konamiIds = $input->getOption('konami_ids');
        $withTranslations = $input->getOption('with_translations');
        $withImages = $input->getOption('with_images');

        if (false === $all && null === $ygoproIds && null === $konamiIds) {
            $output->writeln('<error>Provide an id or use de all flag</error>');

            return self::INVALID;
        }

        if (true === $all && (null !== $ygoproIds || null !== $konamiIds)) {
            $output->writeln('<error>Dont mix the all flag with an id</error>');

            return self::INVALID;
        }

        $ids = $this->importData($all, $ygoproIds, $konamiIds);

        /** @var array<Card> $cards */
        $cards = $this->cards($all, $ids);

        if ($withTranslations) {
            $this->importTranslations(...$cards);
        }

        if ($withImages) {
            $this->importImages(...$cards);
        }

        return self::SUCCESS;
    }

    /** @return array<string> */
    private function importData(bool $all, ?string $ygoproIds, ?string $konamiIds): array
    {
        $search = [
            'misc' => 'yes'
        ];

        if (true === $all) {
            $search['format'] = 'Speed Duel';
        }

        if ($ygoproIds) {
            $search['id'] = $ygoproIds;
        }

        if ($konamiIds) {
            $search['konami_id'] = $konamiIds;
        }

        try {
            $response = $this->ygoproClient
                ->request(Request::METHOD_GET, '/api/v7/cardinfo.php', ['query' => $search])
                ->toArray();
        } catch (\Throwable $e) {
            $this->output->writeln('<Error>Error on ygopro request</Error>');
            $this->output->writeln($e->getMessage());

            return [];
        }

        $ids = [];

        foreach ($response['data'] as $cardData) {
            if (\in_array($cardData['frameType'], ['link', 'token', 'effect_pendulum'], true)) {
                continue;
            }


            $card = Card::fromYgoProArray($cardData);

            new Criteria(
                null,
                null,
                null,
                new Filters(
                    FilterType::AND,
                    FilterType::AND,
                    new Filter(
                        new ArrayElementFilterField($name, $index),
                        new FilterField('refs'),
                        new ArrayElementFilterValue((string) $card->references->ygoProId),
                        FilterOperator::EQUAL,
                    ),
                ),
            );

            $this->cardRepository->search();

            $this->cardRepository->save($card);
            $ids[] = $card->id->value();
        }

        return $ids;
    }

    private function cards(bool $all, array $ids): array
    {
        $filters = [];

        if (false === $all) {
            $filters[] = new Filters(
                FilterType::AND,
                FilterType::AND,
                new Filter(
                    new FilterField('id'),
                    new StringArrayFilterValue(...$ids),
                    FilterOperator::IN,
                ),
            );
        }

        return $this->cardRepository->search(new Criteria(null, null, null, ...$filters));
    }

    private function importTranslations(Card ...$cards): void
    {
        foreach ($cards as $card) {
            $this->output->writeln($card->name->get(Locale::en_GB));

            if (null === $card->references->konamiId) {
                $this->output->writeln('Invalid konami id');

                continue;
            }

            $translation = $this->cardTranslationRepository->byKonamiId($card->references->konamiId);

            if (null === $translation) {
                $this->output->writeln($card->id . ' - ' . $card->name->get(Locale::en_GB) . ' | Translation missing, importing');

                $translation = $this->ygorganizationClient
                    ->request(Request::METHOD_GET, '/data/card/' . (string) $card->references->konamiId)
                    ->toArray();

                $this->cardTranslationRepository->save($card->references->konamiId, $translation);
            } else {
                $this->output->writeln($card->id . ' - ' . $card->name->get(Locale::en_GB) . ' | Translation exists');

                $translation = Json::decode($translation['data']);
            }

            if (false === \array_key_exists('es', $translation['cardData'])) {
                $this->output->writeln('ES translation not found');

                continue;
            }

            $es = $translation['cardData']['es'];

            $card->name = LocalizedString::fromArray(
                [
                    Locale::en_GB->value => $card->name->get(Locale::en_GB),
                    Locale::es_ES->value => $es['name'],
                ],
            );

            $card->description = LocalizedString::fromArray(
                [
                    Locale::en_GB->value => $card->description->get(Locale::en_GB),
                    Locale::es_ES->value => $es['effectText'],
                ],
            );

            $this->cardRepository->save($card);
        }
    }

    private function importImages(Card ...$cards): void
    {
        foreach ($cards as $card) {
            $this->importImage('cards', $card);
            $this->importImage('cards_small', $card);
            $this->importImage('cards_cropped', $card);

            $this->output->writeln($card->id . ' - ' . $card->name->get() . ' | Images imported');
        }
    }

    private function importImage(string $type, Card $card): void
    {
        if (\file_exists('public/assets/ygo/' . $type . '/' . $card->id->value() . '.jpg')) {
            if ($this->output->isVerbose()) {
                $this->output->writeln($card->id . ' - ' . $card->name->get() . ' | ' . $type . ' | already imported');
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
