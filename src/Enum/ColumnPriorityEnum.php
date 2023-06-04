<?php

declare(strict_types=1);

namespace Drupal\accordion_table\Enum;

class ColumnPriorityEnum {
  const LOW = 'low';
  const MEDIUM = 'medium';
  const HIGH = 'high';

  public static function getValues(): array {
    return [
      static::LOW,
      static::MEDIUM,
      static::HIGH,
    ];
  }

}
