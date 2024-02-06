<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CardCategoryTrap: string implements \JsonSerializable
{
    use EnumHelper;

    case NORMAL = 'Normal';
    case CONTINUOUS = 'Continuous';
    case COUNTER = 'Counter';
}
