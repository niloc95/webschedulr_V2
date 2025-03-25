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

class Migration_Add_calendar_view_setting extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('calendar_view', 'user_settings')) {
            $fields = [
                'calendar_view' => [
                    'type' => 'VARCHAR',
                    'constraint' => '32',
                    'default' => 'default',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);

            $this->db->update('user_settings', ['calendar_view' => 'default']);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('calendar_view', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'calendar_view');
        }
    }
}
