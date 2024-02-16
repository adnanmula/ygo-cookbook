<?php
declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

final readonly class LocalizedString implements \JsonSerializable
{
    private const DEFAULT_LOCALE = Locale::es_ES;

    private function __construct(private array $values)
    {
    }

    public static function fromLocale(string $value, Locale $locale = Locale::es_ES): self
    {
        return new self([$locale->value => $value]);
    }

    public static function fromArray(array $values): self
    {
        return new self($values);
    }

    public function get(Locale $locale = self::DEFAULT_LOCALE): ?string
    {
        return $this->values[$locale->value] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->values;
    }

    public function __toString(): string
    {
        return $this->get(self::DEFAULT_LOCALE);
    }
}
