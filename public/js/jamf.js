/**
 * -------------------------------------------------------------------------
 * JAMF plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of JAMF plugin for GLPI.
 *
 * JAMF plugin for GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * JAMF plugin for GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JAMF plugin for GLPI. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2024-2025 by Teclib'
 * @copyright Copyright (C) 2019-2024 by Curtis Conard
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/jamf
 * -------------------------------------------------------------------------
 */

/* global GLPI_PLUGINS_PATH */
(function () {
    window.JamfPlugin = function () {
        /**
         * All possible MDM commands for the item on this page (if applicable).
         * @since 1.1.0
         * @type {{}}
         */
        this.commands = {};

        this.dialog_confirm_command = null;

        this.dialog_send_command = null;

        this.jamf_id = -1;

        this.itemtype = null;

        this.items_id = -1;

        /**
         * The AJAX directory.
         * @since 1.1.0
         * @type {string}
         */
        this.ajax_root = '';

        this.init = (args) => {
            if (args !== undefined && args.commands !== undefined) {
                this.commands = args.commands;
                this.jamf_id = args.jamf_id;
                this.itemtype = args.itemtype;
                this.items_id = args.items_id;
                this.ajax_root = args.ajax_root || `${CFG_GLPI.root_doc  }/plugins/myplugin/ajax/`;
            }
        };

        this.onMDMCommandButtonClick = (command, event) => {
            event.preventDefault();
            if (this.commands[command] !== undefined) {
                if (this.commands[command]['params'] !== undefined) {
                    showMDMCommandForm(command);
                } else if (this.commands[command]['confirm'] !== undefined && this.commands[command]['confirm'] === true) {
                    showMDMCommandConfirmation(command);
                } else {
                    this.sendMDMCommand(command);
                }
            }
        };

        /**
         *
         * @param {Object} command
         */
        const showMDMCommandForm = (command) => {
            $.ajax({
                method: 'GET',
                url: (`${this.ajax_root  }getMDMCommandForm.php`),
                data: {
                    command: command,
                    jamf_id: this.jamf_id,
                    itemtype: this.itemtype,
                    items_id: this.items_id
                }
            }).done((data) => {
                if (data !== undefined && data !== null) {
                    if (this.dialog_send_command !== undefined && this.dialog_send_command !== null) {
                        this.dialog_send_command.remove();
                    }
                    this.dialog_send_command = $(`
<div class="modal" role="dialog">
   <div class="modal-dialog" role="dialog">
       <div class="modal-content">
          <div class="modal-body">${data}</div>
          <div class="modal-footer">
             <button type="button" name="close" class="btn btn-secondary" data-dismiss="modal">${__('Cancel')}</button>
             <button type="button" name="send" class="btn btn-primary">${__('Send')}</button>
          </div>
       </div>
    </div>
</div>`).appendTo('#page');
                    this.dialog_send_command.on('click', 'button[name="send"]', () => {
                        if (this.commands[command]['confirm'] !== undefined && this.commands[command]['confirm'] === true) {
                            showMDMCommandConfirmation(command, this.dialog_send_command.serialize());
                        } else {
                            this.sendMDMCommand(command, this.dialog_send_command.serialize());
                        }
                    });
                    this.dialog_send_command.on('click', 'button[name="close"]', (e) => {
                        $(e.target).closest('.modal').modal('hide');
                    });
                    this.dialog_send_command.modal('show');
                }
            });
        };

        const showMDMCommandConfirmation = (command, params) => {
            if (this.dialog_confirm_command === undefined || this.dialog_confirm_command === null) {
                const warn_text = _x('message', 'Are you sure you want to send the command: %s?', 'jamf').replace("%s", _x('mdm_command', this.commands[command].name, 'jamf'));

                this.dialog_confirm_command = $(`
<div class="modal" role="dialog">
   <div class="modal-dialog" role="dialog">
       <div class="modal-content">
          <div class="modal-body">${warn_text}</div>
          <div class="modal-footer">
             <button type="button" name="close" class="btn btn-secondary" data-dismiss="modal">${__('Cancel')}</button>
             <button type="button" name="confirm" class="btn btn-primary">${__('Confirm')}</button>
          </div>
       </div>
    </div>
</div>`).appendTo('#page');

                this.dialog_confirm_command.on('click', 'button[name="confirm"]', () => {
                    this.sendMDMCommand(command, params);
                });
                this.dialog_confirm_command.on('click', 'button[name="close"]', (e) => {
                    $(e.target).closest('.modal').modal('hide');
                });
                this.dialog_confirm_command.modal('show');
            }
        };

        /**
         *
         */
        this.sendMDMCommand = (command, params) => {
            if (params === undefined) {
                params = '';
            }
            $.ajax({
                method: 'POST',
                url: (`${this.ajax_root  }sendMDMCommand.php`),
                data: {
                    command: command,
                    fields: params,
                    jamf_id: this.jamf_id,
                    itemtype: this.itemtype,
                    items_id: this.items_id
                }
            }).always(() => {
                location.reload();
            });
        };
    };
})();

(function () {
    /**
     * Replace the button content by a spinner and a label, and disable it.
     * The label comes from the button's data-jamf-busy-label attribute.
     */
    const setBusy = (button, spinner_class, text_prefix) => {
        button.prop('disabled', true).empty()
            .append($('<span>', {
                class: `spinner-border spinner-border-sm ${spinner_class}`,
                role: 'status',
                'aria-hidden': 'true'
            }))
            .append(document.createTextNode(text_prefix + button.data('jamfBusyLabel')));
    };

    const importDevices = () => {
        const import_ids = $(':checkbox:checked').filter(':not([name^="_checkall"])').map(function () {
            return this.name.replace("import", "").substring(1).split('_');
        }).toArray();

        $.ajax({
            type: "POST",
            url: `${CFG_GLPI.root_doc}/plugins/jamf/ajax/import.php`,
            data: {
                action: "import",
                import_ids: import_ids
            },
            contentType: 'application/json',
            beforeSend: () => {
                setBusy($('[data-jamf-action="import"]'), 'me-2', '');
                $('[data-jamf-action="discover"]').prop('disabled', true);
                $('[data-jamf-action="clear"]').prop('disabled', true);
            },
            complete: () => {
                window.location.reload();
            }
        });
    };

    const discoverNow = () => {
        $.ajax({
            type: "POST",
            url: `${CFG_GLPI.root_doc}/plugins/jamf/ajax/cron.php`,
            data: { crontask: "importJamf" },
            contentType: 'application/json',
            beforeSend: () => {
                $('[data-jamf-action="import"]').prop('disabled', true);
                setBusy($('[data-jamf-action="discover"]'), 'me-2', '');
                $('[data-jamf-action="clear"]').prop('disabled', true);
            },
            complete: () => {
                window.location.reload();
            }
        });
    };

    const clearPendingImports = () => {
        $.ajax({
            type: "POST",
            url: `${CFG_GLPI.root_doc}/plugins/jamf/ajax/import.php`,
            data: {
                action: "clear"
            },
            contentType: 'application/json',
            beforeSend: () => {
                $('[data-jamf-action="import"]').prop('disabled', true);
                $('[data-jamf-action="discover"]').prop('disabled', true);
                setBusy($('[data-jamf-action="clear"]'), 'me-2', '');
            },
            complete: () => {
                window.location.reload();
            }
        });
    };

    const mergeDevices = () => {
        const post_data = {};
        const table = $("#merge_table")[0];
        const row_count = table.rows.length;
        for (let i = 1; i < row_count; i++) {
            const row = table.rows[i];
            const jamf_id = row.cells[0].innerText;
            const itemtype = row.cells[2].innerText;
            const jamf_type = row.cells[3].innerText;
            const glpi_sel = $(row.cells[6]).find('select')[0];
            const glpi_id = glpi_sel.value;
            if (glpi_id && glpi_id > 0) {
                post_data[glpi_id] = {'itemtype': itemtype, 'jamf_id': jamf_id, 'jamf_type': jamf_type};
            }
        }
        $.ajax({
            type: "POST",
            url: `${CFG_GLPI.root_doc}/plugins/jamf/ajax/merge.php`,
            data: {action: "merge", item_ids: post_data},
            contentType: 'application/json',
            beforeSend: () => {
                setBusy($('[data-jamf-action="merge"]'), '', ' ');
            },
            complete: () => {
                location.reload();
            }
        });
    };

    $(document).on('click', '[data-jamf-action="import"]', () => importDevices());
    $(document).on('click', '[data-jamf-action="discover"]', () => discoverNow());
    $(document).on('click', '[data-jamf-action="clear"]', () => clearPendingImports());
    $(document).on('click', '[data-jamf-action="merge"]', () => mergeDevices());

    $(document).on('click', '[data-jamf-action="reset-password"]', (e) => {
        const password_input = $('input[name="jsspassword"]');
        $(e.currentTarget).closest('div.form-field').hide();
        password_input.val('');
        password_input.closest('div.form-field').attr('hidden', null);
    });

    $(document).on('click', '[data-jamf-sync]', function () {
        $.ajax({
            type: "POST",
            url: `${CFG_GLPI.root_doc}/plugins/jamf/ajax/sync.php`,
            data: {"itemtype": this.dataset.jamfSyncItemtype, "items_id": this.dataset.jamfSyncItemsId},
            contentType: 'application/json',
            success: () => {
                location.reload();
            }
        });
    });

    const initMdmCommands = () => {
        $('[data-jamf-mdm-commands]:not([data-jamf-mdm-ready])').each(function () {
            $(this).attr('data-jamf-mdm-ready', '');
            window.jamfPlugin = new JamfPlugin();
            window.jamfPlugin.init({
                commands: JSON.parse(this.dataset.jamfMdmCommands),
                jamf_id: this.dataset.jamfId,
                itemtype: this.dataset.jamfItemtype,
                items_id: this.dataset.jamfItemsId,
            });
        });
    };

    $(document).on('click', '[data-jamf-mdm-command]', (e) => {
        window.jamfPlugin.onMDMCommandButtonClick(e.currentTarget.dataset.jamfMdmCommand, e);
    });

    const initInventoryInfo = () => {
        const markers = $('[data-jamf-inventory-info]:not([data-jamf-inventory-info-ready])');
        if (markers.length === 0) {
            return;
        }
        markers.attr('data-jamf-inventory-info-ready', '');
        // Assets are marked dynamic by this plugin, so the native inventory section is removed
        const card_title_icon = $('form[name="asset_form"]').next('.card').find('.card-header .ribbon i');
        if (card_title_icon.length > 0 && card_title_icon.eq(0).hasClass('fa-cloud-download-alt')) {
            card_title_icon.closest('.card').remove();
        }
    };

    const initPluginMarkup = () => {
        initMdmCommands();
        initInventoryInfo();
    };

    $(initPluginMarkup);
    $(document).on('glpi.tab.loaded', initPluginMarkup);
})();
