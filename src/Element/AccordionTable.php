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

}
