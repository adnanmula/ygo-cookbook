<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Ygo;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class CardTranslationDbalRepository extends DbalRepository
{
    private const TABLE = 'ygo_card_translations';

    public function save(int $konamiId, array $data): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'INSERT INTO %s (konami_id, data) VALUES (:konami_id, :data) ON CONFLICT (konami_id) DO NOTHING',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':konami_id', $konamiId);
        $stmt->bindValue(':data', Json::encode($data));

        $stmt->executeStatement();
    }

    public function byKonamiId(int $konamiId): ?array
    {
        $builder = $this->connection->createQueryBuilder();

        $result = $builder->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.konami_id = :id')
            ->setParameter('id', $konamiId)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $result;
    }
}
