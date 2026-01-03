<?php

namespace App\Settings;

final class GroupedSettings
{
    /**
     * @param array<int, array<string, mixed>> $definitions
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function group(array $definitions): array
    {
        $grouped = [];

        foreach ($definitions as $definition) {
            $group = $definition['group_slug'] ?? 'general';
            $grouped[$group][] = $definition;
        }

        return $grouped;
    }
}
