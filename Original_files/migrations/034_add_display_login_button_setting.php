<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N. Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_display_login_button_setting extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->get_where('settings', ['name' => 'display_login_button'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'display_login_button',
                'value' => '1',
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->get_where('settings', ['name' => 'display_login_button'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'display_login_button']);
        }
    }
}
