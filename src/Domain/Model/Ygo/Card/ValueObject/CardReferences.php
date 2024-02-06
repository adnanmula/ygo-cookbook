<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

final readonly class CardReferences implements \JsonSerializable
{
    public function __construct(
        public ?int $konamiId,
        public int $ygoProId
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'konami_id' => $this->konamiId,
            'ygopro_id' => $this->ygoProId,
        ];
    }
}