<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Ygo;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\Format;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class CardDbalRepository extends DbalRepository implements CardRepository
{
    private const TABLE = 'ygo_cards';

    public function save(Card $card): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, refs, name, description, supertype, type, frame_type, wiki_url, atk, def, level, category, attribute, pend_scale, link_value, formats)
                    VALUES (:id, :refs, :name, :description, :supertype, :type, :frame_type, :wiki_url, :atk, :def, :level, :category, :attribute, :pend_scale, :link_value, :formats)
                    ON CONFLICT (id) DO NOTHING
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $card->id);
        $stmt->bindValue(':refs', Json::encode($card->references));
        $stmt->bindValue(':name', Json::encode($card->name));
        $stmt->bindValue(':description', Json::encode($card->description));
        $stmt->bindValue(':supertype', $card->supertype->value);
        $stmt->bindValue(':type', $card->type->value);
        $stmt->bindValue(':frame_type', $card->frameType->value);
        $stmt->bindValue(':wiki_url', $card->wikiUrl->value());
        $stmt->bindValue(':formats', Json::encode(array_map(static fn (Format $f) => $f->value, $card->formats)));
        $stmt->bindValue(':atk', $card->atk);
        $stmt->bindValue(':def', $card->def);
        $stmt->bindValue(':level', $card->level);
        $stmt->bindValue(':category', $card->category);
        $stmt->bindValue(':attribute', $card->attribute?->value);
        $stmt->bindValue(':pend_scale', null);
        $stmt->bindValue(':link_value', null);

        $stmt->executeStatement();
    }
}