<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Ygo;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\LocalizedString;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardAttribute;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardFrameType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardReferences;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardSuperType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\Format;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\WikiUrl;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;

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
                    ON CONFLICT (id) DO UPDATE SET
                        name = :name,
                        description = :description
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $card->id->value());
        $stmt->bindValue(':refs', Json::encode($card->references));
        $stmt->bindValue(':name', Json::encode($card->name->jsonSerialize()));
        $stmt->bindValue(':description', Json::encode($card->description->jsonSerialize()));
        $stmt->bindValue(':supertype', $card->supertype->value);
        $stmt->bindValue(':type', $card->type->value);
        $stmt->bindValue(':frame_type', $card->frameType->value);
        $stmt->bindValue(':wiki_url', $card->wikiUrl->value());
        $stmt->bindValue(':formats', Json::encode(\array_map(static fn (Format $f) => $f->value, $card->formats)));
        $stmt->bindValue(':atk', $card->atk);
        $stmt->bindValue(':def', $card->def);
        $stmt->bindValue(':level', $card->level);
        $stmt->bindValue(':category', $card->category);
        $stmt->bindValue(':attribute', $card->attribute?->value);
        $stmt->bindValue(':pend_scale', null);
        $stmt->bindValue(':link_value', null);

        $stmt->executeStatement();
    }

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    private function map(array $row): Card
    {
        $refs = Json::decode($row['refs']);

        return new Card(
            Uuid::from($row['id']),
            new CardReferences(
                $refs['konami_id'],
                $refs['ygopro_id'],
            ),
            CardSuperType::from($row['supertype']),
            LocalizedString::fromArray(Json::decode($row['name'])),
            LocalizedString::fromArray(Json::decode($row['description'])),
            CardType::from($row['type']),
            CardFrameType::from($row['frame_type']),
            WikiUrl::from($row['wiki_url']),
            $row['category'],
            $row['atk'],
            $row['def'],
            $row['level'],
            null === $row['attribute']
                ? null
                : CardAttribute::from($row['attribute']),
            ...\array_map(static fn (string $f) => Format::from($f), Json::decode($row['formats'])),
        );
    }
}
