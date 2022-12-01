(function (Drupal) {
  let moduleSettings = {};
  const rowSelector = '.accordion-table.responsive.has-result tr.view';

  function toggleOpen(event) {
    // Do nothing if a link or button is clicked within the cell.
    if (
      event.target.tagName === 'A'
      || event.target.tagName === 'INPUT'
      || event.target.tagName === 'BUTTON'
      || event.target.classList.contains('dropbutton-arrow')
    ) {
      return;
    }

    const parentRow = event.target.closest(rowSelector);
    if (!parentRow) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    if (!moduleSettings.allow_multiple_open_rows) {
      parentRow.parentNode.querySelectorAll('tr.view').forEach((row) => {
        if (row !== parentRow) {
          row.classList.remove('open');
        }
      })
    }

    parentRow.classList.toggle('open');
  }

  function enableExpandTrigger() {
    const viewRows = document.querySelectorAll(rowSelector);

    viewRows.forEach((row) => {
      row.addEventListener('click', toggleOpen);
    });
  }

  function disableExpandTrigger() {
    const viewRows = document.querySelectorAll(rowSelector);

    viewRows.forEach((row) => {
      row.removeEventListener('click', toggleOpen);
    });
  }

  function toggleExpandTrigger() {
    if (window.matchMedia(moduleSettings.priority_mq_mapping.low).matches) {
      return disableExpandTrigger();
    }

    return enableExpandTrigger();
  }

  Drupal.behaviors.accordionTable = {
    attach: (context, settings) => {
      moduleSettings = settings.accordion_table;
      if (!moduleSettings.priority_mq_mapping) {
        console.error('You need to configure the settings for the accordion_table module before using it.');

        return;
      }

      toggleExpandTrigger();

      window.addEventListener('themeBreakpoint:changed', toggleExpandTrigger)
    },
    detach: (context, settings) => {
      moduleSettings = settings.accordion_table;
      if (!moduleSettings.priority_mq_mapping) {
        return;
      }

      disableExpandTrigger();

      window.removeEventListener('themeBreakpoint:changed', toggleExpandTrigger)
    }
  };
})(Drupal);
