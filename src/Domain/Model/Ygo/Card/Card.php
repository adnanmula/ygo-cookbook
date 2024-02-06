<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\LocalizedString;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardAttribute;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardFrameType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardReferences;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardSuperType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\Format;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\WikiUrl;

final class Card implements \JsonSerializable
{
    public array $formats;

    public function __construct(
        public Uuid $id,
        public CardReferences $references,
        public CardSuperType $supertype,
        public LocalizedString $name,
        public LocalizedString $description,
        public CardType $type,
        public CardFrameType $frameType,
        public WikiUrl $wikiUrl,
        public string $category,
        public ?int $atk,
        public ?int $def,
        public ?int $level,
        public ?CardAttribute $attribute,
        Format ...$formats,
    ) {
        $this->formats = $formats;
    }

    public static function fromYgoProArray(array $data): self
    {
        $frameType = CardFrameType::from($data['frameType']);

        return new self(
            Uuid::v4(),
            new CardReferences(
                $data['misc_info'][0]['konami_id'] ?? null,
                $data['id'],
            ),
            CardSuperType::fromFrameType($frameType),
            LocalizedString::fromLocale($data['name'], Locale::en_GB),
            LocalizedString::fromLocale($data['desc'], Locale::en_GB),
            CardType::from($data['type']),
            $frameType,
            WikiUrl::from($data['ygoprodeck_url']),
            $data['race'],
            $data['atk'] ?? null,
            $data['def'] ?? null,
            $data['level'] ?? null,
            \array_key_exists('attribute', $data)
                ? CardAttribute::from($data['attribute'])
                : null,
            ...\array_map(static fn (string $format) => Format::from($format), $data['misc_info'][0]['formats']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'references' => $this->references->jsonSerialize(),
            'supertype' => $this->supertype->jsonSerialize(),
            'name' => $this->name->jsonSerialize(),
            'description' => $this->description->jsonSerialize(),
            'type' => $this->type->jsonSerialize(),
            'frameType' => $this->frameType->jsonSerialize(),
            'wikiUrl' => $this->wikiUrl->jsonSerialize(),
            'category' => $this->category,
            'atk' => $this->atk,
            'def' => $this->def,
            'level' => $this->level,
            'attribute' => $this->attribute?->jsonSerialize(),
            'formats' => array_map(static fn (Format $f) => $f->value, $this->formats),
        ];
    }
}
