<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\StringValueObject;
use Assert\Assert;

final class WikiUrl extends StringValueObject
{
    public static function from(string $value): static
    {
        Assert::that($value)->url()->startsWith('https://ygoprodeck.com/card/');

        return new self($value);
    }
}