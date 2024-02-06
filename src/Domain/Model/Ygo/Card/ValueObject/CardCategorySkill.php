<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\StringValueObject;

final class CardCategorySkill extends StringValueObject
{
    public static function from(string $value): static
    {
        return new static($value);
    }
}
