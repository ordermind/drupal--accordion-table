<?php

declare(strict_types=1);

namespace Drupal\accordion_table\Plugin\views\style;

use Drupal\accordion_table\Enum\ViewsColumnPriorityEnum;
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

    $options['table_class'] = ['default' => ''];
    $options['separate_operations'] = ['default' => FALSE];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['table_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Table class'),
      '#default_value' => $this->options['table_class'],
      '#weight' => 0,
    ];

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

    $view = $build[0]['#view'];
    $uniqueId = 'accordion-table--views-' . $view->id() . '-' . $view->getDisplay()->getPluginId();

    $build[0]['#view']->style_plugin->options['accordion_table_id'] = $uniqueId;

    $build['#attached']['library'][] = 'accordion_table/accordion_table';
    $build['#attached']['drupalSettings']['accordion_table'] = $this->drupalSettingsFactory->create();

    $build['#attached']['html_head'][] = [
      [
        '#tag' => 'style',
        '#value' => $this->cssFactory->create($this->getLowestColumnPriority(), $uniqueId),
      ],
      'accordion_table',
    ];

    return $build;
  }

  private function getLowestColumnPriority() {
    $priorities = [
      ViewsColumnPriorityEnum::LOW,
      ViewsColumnPriorityEnum::MEDIUM,
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
