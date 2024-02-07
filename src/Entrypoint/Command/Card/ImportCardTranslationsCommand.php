<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\LocalizedString;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\Ygo\CardTranslationDbalRepository;
use AdnanMula\Criteria\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportCardTranslationsCommand extends Command
{
    public const NAME = 'import:cards:translations';

    public function __construct(
        private readonly HttpClientInterface $ygorganizationClient,
        private readonly CardRepository $cardRepository,
        private readonly CardTranslationDbalRepository $cardTranslationRepository,
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

        foreach ($cards as $card) {
            $output->writeln($card->name->get(Locale::en_GB));

            if (null === $card->references->konamiId) {
                $output->writeln('Invalid konami id');

                continue;
            }

            $translation = $this->cardTranslationRepository->byKonamiId($card->references->konamiId);

            if (null === $translation) {
                $translation = $this->ygorganizationClient
                    ->request(Request::METHOD_GET, '/data/card/' . (string) $card->references->konamiId)
                    ->toArray();

                $this->cardTranslationRepository->save($card->references->konamiId, $translation);
            } else {
                $translation = Json::decode($translation['data']);
            }

            if (false === \array_key_exists('es', $translation['cardData'])) {
                $output->writeln('ES translation not found');

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

        return self::SUCCESS;
    }
}
