<?php

namespace Drupal\accordion_table\Factory;

use Drupal\accordion_table\Enum\ColumnPriorityEnum;
use Drupal\accordion_table\Enum\ViewsColumnPriorityEnum;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\theme_breakpoints_js\ThemeBreakpointsJs;

/**
 * Creates theme-specific css for accordion table.
 */
class CssFactory {
  protected ConfigFactoryInterface $configFactory;
  protected ThemeManagerInterface $themeManager;
  protected ThemeBreakpointsJs $themeBreakpointsJs;

  public function __construct(ConfigFactoryInterface $configFactory, ThemeManagerInterface $themeManager, ThemeBreakpointsJs $themeBreakpointsJs) {
    $this->configFactory = $configFactory;
    $this->themeManager = $themeManager;
    $this->themeBreakpointsJs = $themeBreakpointsJs;
  }

  public function create(string $lowestPriority, string $uniqueId): string {
    $activeTheme = $this->themeManager->getActiveTheme();
    $activeThemeName = $activeTheme->getName();
    $breakpoints = $this->themeBreakpointsJs->getBreakpoints($activeThemeName);
    $config = $this->configFactory->get('accordion_table.settings');
    $themeMapping = $config->get('priority_breakpoint_mapping');
    $currentThemeMapping = $themeMapping[$activeThemeName] ?? NULL;
    if (!$currentThemeMapping) {
      return '';
    }

    $lowPriorityMediaQuery = $this->getLowPriorityMediaQuery($breakpoints, $currentThemeMapping, $lowestPriority);
    $mediumPriorityMediaQuery = $this->getMediumPriorityMediaQuery($breakpoints, $currentThemeMapping, $lowestPriority);

    $uniqueIdAttribute = "data-accordion-table-id=\"${uniqueId}\"";

    return <<<EOT

/* Dynamically generated CSS for accordion table depending on theme breakpoints */

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view {
  position: relative;
  cursor: pointer;
}

.accordion-table.responsive[$uniqueIdAttribute] tr th.priority-low,
.accordion-table.responsive[$uniqueIdAttribute] tr td.priority-low,
.accordion-table.responsive[$uniqueIdAttribute] tr th.priority-medium,
.accordion-table.responsive[$uniqueIdAttribute] tr td.priority-medium {
  display: none;
}

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.fold {
  display: none;
}

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.fold .column {
  display: none;
}

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.fold .column.priority-low,
.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.fold .column.priority-medium {
  display: block;
}

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view.open + tr.fold {
  display: table-row;
}

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view td {
  position: relative;
}

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view td:first-of-type::after {
  content: "";
  position: absolute;
  left: 2px;
  top: calc(50% - 3px);
  width: 0;
  height: 0;
  border-top: 4px solid transparent;
  border-bottom: 4px solid transparent;
  border-left: 5px solid #999;
}

.accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view.open td:first-of-type::after {
  left: 0;
  top: calc(50% - 1px);

  border-top: 5px solid #999;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
}

@media $mediumPriorityMediaQuery {
  .accordion-table.responsive[$uniqueIdAttribute] tr th.priority-medium,
  .accordion-table.responsive[$uniqueIdAttribute] tr td.priority-medium {
    display: table-cell;
  }

  .accordion-table.responsive.has-result[$uniqueIdAttribute] tr.fold .column.priority-medium {
    display: none;
  }
}

@media $lowPriorityMediaQuery {
  .accordion-table.responsive[$uniqueIdAttribute] tr th.priority-low,
  .accordion-table.responsive[$uniqueIdAttribute] tr td.priority-low {
    display: table-cell;
  }

  .accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view {
    cursor: default;
  }

  .accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view.open + tr.fold {
    display: none;
  }

  .accordion-table.responsive.has-result[$uniqueIdAttribute] tr.fold .column.priority-low {
    display: none;
  }

  .accordion-table.responsive.has-result[$uniqueIdAttribute] tr.view td:first-of-type::after {
    display: none;
  }
}
EOT;
  }

  private function getLowPriorityMediaQuery(array $breakpoints, array $currentThemeMapping, string $lowestPriority): string {
    if (
      in_array(
        $lowestPriority,
        [
          ColumnPriorityEnum::LOW,
          ViewsColumnPriorityEnum::LOW,
        ]
      )
    ) {
      return $breakpoints[$currentThemeMapping[ColumnPriorityEnum::LOW]]->getMediaQuery();
    }

    if (
      in_array(
        $lowestPriority,
        [
          ColumnPriorityEnum::MEDIUM,
          ViewsColumnPriorityEnum::MEDIUM,
        ]
      )
    ) {
      return $breakpoints[$currentThemeMapping[ColumnPriorityEnum::MEDIUM]]->getMediaQuery();
    }

    return 'screen';
  }

  private function getMediumPriorityMediaQuery(array $breakpoints, array $currentThemeMapping, string $lowestPriority): string {
    if (
      in_array(
        $lowestPriority,
        [
          ColumnPriorityEnum::LOW,
          ViewsColumnPriorityEnum::LOW,
          ColumnPriorityEnum::MEDIUM,
          ViewsColumnPriorityEnum::MEDIUM,
        ]
      )
    ) {
      return $breakpoints[$currentThemeMapping[ColumnPriorityEnum::MEDIUM]]->getMediaQuery();
    }

    return 'screen';
  }

}
