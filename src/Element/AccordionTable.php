<?php

declare(strict_types=1);

namespace Drupal\accordion_table\Element;

use Drupal\accordion_table\Enum\ColumnPriorityEnum;
use Drupal\Component\Utility\Html;
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

    $uniqueId = Html::getUniqueId('accordion-table--custom-element');
    $build['#attributes']['data-accordion-table-id'] = $uniqueId;

    $build['#inline_css'] = $cssFactory->create(static::getLowestColumnPriority($build['#column_priorities'] ?? []), $uniqueId);

    $build['#attached']['library'][] = 'accordion_table/accordion_table';
    $build['#attached']['drupalSettings']['accordion_table'] = $drupalSettingsFactory->create();

    return $build;
  }

  private static function getLowestColumnPriority(array $columnPriorities) {
    $priorities = ColumnPriorityEnum::getValues();

    $lowestPriorityIndex = count($priorities) - 1;

    foreach ($columnPriorities as $priority) {
      $priorityIndex = array_search($priority, $priorities);
      if ($priorityIndex !== FALSE && $priorityIndex < $lowestPriorityIndex) {
        $lowestPriorityIndex = $priorityIndex;
      }
    }

    return $priorities[$lowestPriorityIndex];
  }

}
