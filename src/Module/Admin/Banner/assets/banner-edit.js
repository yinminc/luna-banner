/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

import '@main';

u.$ui.bootstrap.tooltip();

const formId = '#admin-form';

// Validation
u.formValidation().then(() => {
  u.$ui.disableOnSubmit(formId);
});

// Init form
u.form(formId).initComponent();

// Disable if uploading
u.$ui.disableIfStackNotEmpty();

// Keep Alive
u.$ui.keepAlive(location.href);
