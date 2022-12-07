<?php

declare(strict_types=1);

namespace Drupal\accordion_table\HookHandlers;

class ThemeHandler {

  public function execute(array $existing, string $type, string $theme, string $path): array {
    $coreTheme = drupal_common_theme($existing, $type, $theme, $path);

    return ['accordion_table' => $coreTheme['table']];
  }

}
