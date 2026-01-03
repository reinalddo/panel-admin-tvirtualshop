<?php

namespace App\Settings;

use PDO;

final class SettingDefinitionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function allWithGroups(): array
    {
        $sql = 'SELECT d.*, g.name AS group_name, g.slug AS group_slug
                FROM setting_definitions d
                INNER JOIN setting_groups g ON g.id = d.group_id
                ORDER BY g.sort_order, d.sort_order, d.setting_key';

        $stmt = $this->pdo->query($sql);

        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
