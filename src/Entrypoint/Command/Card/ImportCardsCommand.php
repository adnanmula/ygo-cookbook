<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportCardsCommand extends Command
{
    public const NAME = 'card:import';

    public function __construct(
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Apply predefined tags to decks')
            ->addOption('deck', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        return self::SUCCESS;
    }
}
