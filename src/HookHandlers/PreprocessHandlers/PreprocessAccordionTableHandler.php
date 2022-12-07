<?php

declare(strict_types=1);

namespace Drupal\accordion_table\HookHandlers\PreprocessHandlers;

/**
 * Hook handler for hook_preprocess_accordion_table().
 */
class PreprocessAccordionTableHandler {

  public function preprocess(array &$variables): void {
    template_preprocess_table($variables);

    // $this->addVariables($variables, $view, $options);
    // $this->addTableClasses($variables, $options);
  }

  // Protected function addVariables(array &$variables, ViewExecutable $view, array $options): void {
  //   $variables['separate_operations'] = !empty($options['separate_operations']);
  //   $variables['has_result'] = !empty($view->result);
  // }.
  // Protected function addTableClasses(array &$variables, array $options): void {
  //   $variables['attributes']['class'][] = 'accordion-table';.
  // if (!empty($variables['responsive'])) {
  //     $variables['attributes']['class'][] = 'responsive';
  //   }.
  // if (!empty($variables['has_result'])) {
  //     $variables['attributes']['class'][] = 'has-result';
  //   }.
  // if (!empty($options['table_class'])) {
  //     $variables['attributes']['class'] = array_merge($variables['attributes']['class'], explode(' ', $options['table_class']));
  //   }
  // }.
}
