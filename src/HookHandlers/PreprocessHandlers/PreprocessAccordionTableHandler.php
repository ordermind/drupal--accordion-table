<?php

declare(strict_types=1);

namespace Drupal\accordion_table\HookHandlers\PreprocessHandlers;

/**
 * Hook handler for hook_preprocess_accordion_table().
 */
class PreprocessAccordionTableHandler {

  public function preprocess(array &$variables): void {
    template_preprocess_table($variables);

    $this->addVariables($variables);
    $this->addTableClasses($variables);
  }

  protected function addVariables(array &$variables): void {
    $variables['has_result'] = !empty($variables['rows']);
  }

  protected function addTableClasses(array &$variables): void {
    $variables['attributes']['class'][] = 'accordion-table';
    if (!empty($variables['responsive'])) {
      $variables['attributes']['class'][] = 'responsive';
    }
    if (!empty($variables['has_result'])) {
      $variables['attributes']['class'][] = 'has-result';
    }
    if (!empty($variables['table_classes'])) {
      $variables['attributes']['class'] = array_merge($variables['attributes']['class'], (array) $variables['table_classes']);
    }
  }

}
