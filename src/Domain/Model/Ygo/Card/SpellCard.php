<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card;

use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardCategorySpell;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardFrameType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardSuperType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\WikiUrl;

final readonly class SpellCard extends Card
{
    public function __construct(
        int $id,
        string $name,
        CardType $type,
        CardFrameType $frameType,
        string $description,
        WikiUrl $wikiUrl,
        public CardCategorySpell $category,
    ) {
        parent::__construct($id, $name, $type, $frameType, $description, $wikiUrl);
    }

    public function supertype(): CardSuperType
    {
        return CardSuperType::SPELL;
    }
}
