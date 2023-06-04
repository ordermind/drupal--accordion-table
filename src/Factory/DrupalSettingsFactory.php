<?php

declare(strict_types=1);

namespace Drupal\accordion_table\Factory;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\theme_breakpoints_js\ThemeBreakpointsJs;

/**
 * Creates an array of drupal settings to be passed to the frontend.
 */
class DrupalSettingsFactory {
  protected ConfigFactoryInterface $configFactory;
  protected ThemeManagerInterface $themeManager;
  protected ThemeBreakpointsJs $themeBreakpointsJs;

  public function __construct(ConfigFactoryInterface $configFactory, ThemeManagerInterface $themeManager, ThemeBreakpointsJs $themeBreakpointsJs) {
    $this->configFactory = $configFactory;
    $this->themeManager = $themeManager;
    $this->themeBreakpointsJs = $themeBreakpointsJs;
  }

  public function create(): array {
    $configSettings = $this->configFactory->get('accordion_table.settings');
    $activeTheme = $this->themeManager->getActiveTheme();
    $activeThemeName = $activeTheme->getName();
    $breakpoints = $this->themeBreakpointsJs->getBreakpoints($activeThemeName);
    $config = $this->configFactory->get('accordion_table.settings');
    $themeMapping = $config->get('priority_breakpoint_mapping');
    $currentThemeMapping = $themeMapping[$activeThemeName] ?? NULL;

    if (!$currentThemeMapping) {
      return [];
    }

    return [
      'priority_mq_mapping' => [
        'low' => $breakpoints[$currentThemeMapping['low']]->getMediaQuery(),
        'medium' => $breakpoints[$currentThemeMapping['medium']]->getMediaQuery(),
      ],
      'allow_multiple_open_rows' => $configSettings->get('allow_multiple_open_rows'),
    ];
  }

}
