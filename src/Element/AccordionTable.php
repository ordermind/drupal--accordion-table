<?php

namespace Drupal\accordion_table\Element;

use Drupal\Core\Render\Element\Table;

/**
 * @FormElement("accordion_table")
 */
class AccordionTable extends Table {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'accordion_table',
    ] + parent::getInfo();
  }

  /**
   * {@inheritdoc}
   */
  public static function preRenderTable($element): array {
    /** @var \Drupal\accordion_table\Factory\DrupalSettingsFactory $drupalSettingsFactory */
    $drupalSettingsFactory = \Drupal::service('accordion_table.drupal_settings_factory');
    /** @var \Drupal\accordion_table\Factory\CssFactory $cssFactory */
    $cssFactory = \Drupal::service('accordion_table.css_factory');

    $build = parent::preRenderTable($element);

    $build['#attached']['library'][] = 'accordion_table/accordion_table';
    $build['#attached']['drupalSettings']['accordion_table'] = $drupalSettingsFactory->create();

    $build['#attached']['html_head'][] = [
      [
        '#tag' => 'style',
        '#value' => $cssFactory->create(static::getLowestColumnPriority($build['#columnSettings'] ?? [])),
      ],
      'accordion_table',
    ];

    return $build;
  }

  private static function getLowestColumnPriority(array $columnSettings) {
    $priorities = [
      'low',
      'medium',
      'high',
    ];

    $lowestPriorityIndex = count($priorities) - 1;

    foreach ($columnSettings as $singleColumnSettings) {
      $priorityIndex = array_search($singleColumnSettings['priority'], $priorities);
      if ($priorityIndex !== FALSE && $priorityIndex < $lowestPriorityIndex) {
        $lowestPriorityIndex = $priorityIndex;
      }
    }

    return $priorities[$lowestPriorityIndex];
  }

}
