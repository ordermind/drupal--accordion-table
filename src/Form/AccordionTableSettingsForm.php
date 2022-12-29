<?php

namespace Drupal\accordion_table\Form;

use Drupal\accordion_table\Enum\ColumnPriorityEnum;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\theme_breakpoints_js\ThemeBreakpointsJs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccordionTableSettingsForm extends ConfigFormBase {

  protected ThemeHandlerInterface $themeHandler;
  protected ThemeBreakpointsJs $themeBreakpointsJs;

  public function __construct(ConfigFactoryInterface $configFactory, ThemeHandlerInterface $themeHandler, ThemeBreakpointsJs $themeBreakpointsJs) {
    parent::__construct($configFactory);

    $this->themeHandler = $themeHandler;
    $this->themeBreakpointsJs = $themeBreakpointsJs;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('theme_handler'),
      $container->get('theme_breakpoints_js')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'accordion_table_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $config = $this->config('accordion_table.settings');
    // Get enabled themes and filter out parent themes.
    $enabledThemes = array_filter(
      $this->themeHandler->listInfo(),
      fn (Extension $themeInfo) => empty($themeInfo->sub_themes)
    );
    // Only low and medium priority need to be mapped to breakpoints - high
    // priority columns are always displayed in the table.
    $priorities = [
      ColumnPriorityEnum::LOW,
      ColumnPriorityEnum::MEDIUM,
    ];

    $form['priority_breakpoint_mapping'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Theme mapping'),
      '#description' => $this->t('Please select the breakpoints at which columns with the given priority will become visible in the table.'),
      '#tree' => TRUE,
    ];

    foreach ($enabledThemes as $theme) {
      $themeName = $theme->getName();
      $breakpoints = array_keys($this->themeBreakpointsJs->getBreakpoints($themeName));
      $breakpointOptions = array_combine($breakpoints, $breakpoints);
      $form['priority_breakpoint_mapping'][$themeName] = [
        '#type' => 'details',
        '#title' => $theme->info['name'],
        '#open' => TRUE,
      ];
      foreach ($priorities as $priority) {
        $form['priority_breakpoint_mapping'][$themeName][$priority] = [
          '#type' => 'select',
          '#title' => $this->t('Priority: @priority', ['@priority' => $priority]),
          '#options' => $breakpointOptions,
          '#default_value' => $config->get("priority_breakpoint_mapping.$themeName.$priority"),
          '#required' => TRUE,
        ];
      }
    }

    $form['allow_multiple_open_rows'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow multiple open rows'),
      '#description' => $this->t(
        'If multiple open rows are allowed, they will have to be closed manually. If they are not allowed, the open row will automatically be closed upon opening a new row.'
      ),
      '#default_value' => $config->get('allow_multiple_open_rows'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('accordion_table.settings');
    $config->set('priority_breakpoint_mapping', $form_state->getValue('priority_breakpoint_mapping'));
    $config->set('allow_multiple_open_rows', (bool) $form_state->getValue('allow_multiple_open_rows'));
    $config->save();

    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'accordion_table.settings',
    ];
  }

}
