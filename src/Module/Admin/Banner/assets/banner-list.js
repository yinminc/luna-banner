/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

import '@main';

u.$ui.bootstrap.tooltip();

const formId = '#admin-form';

// Init Grid
u.grid(formId).initComponent();

// Disable on submit
u.$ui.disableOnSubmit(formId);

// Checkbox Multi-select
u.$ui.checkboxesMultiSelect(formId);

// Tooltip
u.$ui.bootstrap.tooltip('.has-tooltip');
