<?php

declare(strict_types=1);

namespace Drupal\accordion_table\HookHandlers\PreprocessHandlers;

use Drupal\views\ViewExecutable;

/**
 * Hook handler for hook_preprocess_views_view_accordiontable().
 */
class PreprocessViewsViewAccordionTableHandler {

  public function preprocess(array &$variables): void {
    template_preprocess_views_view_table($variables);

    /** @var \Drupal\views\ViewExecutable $view */
    $view = $variables['view'];
    $options = $view->style_plugin->options;

    $this->addVariables($variables, $view, $options);
    $this->addTableClasses($variables, $options);
    $this->addTableUniqueId($variables, $options);
  }

  protected function addVariables(array &$variables, ViewExecutable $view, array $options): void {
    $variables['separate_operations'] = !empty($options['separate_operations']);
    $variables['has_result'] = !empty($view->result);
  }

  protected function addTableClasses(array &$variables, array $options): void {
    $variables['attributes']['class'][] = 'accordion-table';

    if (!empty($variables['responsive'])) {
      $variables['attributes']['class'][] = 'responsive';
    }

    if (!empty($variables['has_result'])) {
      $variables['attributes']['class'][] = 'has-result';
    }

    if (!empty($options['table_class'])) {
      $variables['attributes']['class'] = array_merge($variables['attributes']['class'], explode(' ', $options['table_class']));
    }
  }

  protected function addTableUniqueId(array &$variables, array $options): void {
    if (empty($options['accordion_table_id'])) {
      throw new \LogicException('No unique id could be found for the accordion table');
    }

    $variables['attributes']['data-accordion-table-id'] = $options['accordion_table_id'];
  }

}
