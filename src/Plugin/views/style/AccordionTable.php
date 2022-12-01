<?php

namespace Drupal\accordion_table\Plugin\views\style;

use Drupal\accordion_table\Factory\CssFactory;
use Drupal\accordion_table\Factory\DrupalSettingsFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Plugin\views\style\Table;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Style plugin to render each item as a row in an expandable table.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "views_view_accordiontable",
 *   title = @Translation("Accordion Table"),
 *   help = @Translation("Displays rows in an expandable table."),
 *   theme = "views_view_accordiontable",
 *   display_types = {"normal"}
 * )
 */
class AccordionTable extends Table implements ContainerFactoryPluginInterface {
  protected DrupalSettingsFactory $drupalSettingsFactory;
  protected CssFactory $cssFactory;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, DrupalSettingsFactory $drupalSettingsFactory, CssFactory $cssFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->drupalSettingsFactory = $drupalSettingsFactory;
    $this->cssFactory = $cssFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $drupalSettingsFactory = $container->get('accordion_table.drupal_settings_factory');
    $cssFactory = $container->get('accordion_table.css_factory');

    return new static($configuration, $plugin_id, $plugin_definition, $drupalSettingsFactory, $cssFactory);
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['separate_operations'] = ['default' => FALSE];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['separate_operations'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Separate operations buttons'),
      '#default_value' => !empty($this->options['separate_operations']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $build['#attached']['library'][] = 'accordion_table/accordion_table';
    $build['#attached']['drupalSettings']['accordion_table'] = $this->drupalSettingsFactory->create();

    $build['#attached']['html_head'][] = [
      [
        '#tag' => 'style',
        '#value' => $this->cssFactory->create($this->getLowestColumnPriority()),
      ],
      'accordion_table',
    ];

    return $build;
  }

  private function getLowestColumnPriority() {
    $priorities = [
      'priority-low',
      'priority-medium',
      '',
    ];

    $lowestPriorityIndex = count($priorities) - 1;

    foreach ($this->options['info'] as $colSettings) {
      $priorityIndex = array_search($colSettings['responsive'], $priorities);
      if ($priorityIndex !== FALSE && $priorityIndex < $lowestPriorityIndex) {
        $lowestPriorityIndex = $priorityIndex;
      }
    }

    return $priorities[$lowestPriorityIndex];
  }

}
