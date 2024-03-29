<?php

declare(strict_types=1);

namespace Drupal\accordion_table\HookHandlers;

class ThemeHandler {

  public function execute(array $existing, string $type, string $theme, string $path): array {
    $coreTheme = drupal_common_theme($existing, $type, $theme, $path);

    $tableThemeDefinition = $coreTheme['table'];
    $tableThemeDefinition['variables']['inline_css'] = NULL;
    $tableThemeDefinition['variables']['column_priorities'] = [];
    $tableThemeDefinition['variables']['separate_operations'] = FALSE;

    return ['accordion_table' => $tableThemeDefinition];
  }

}
