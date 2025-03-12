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

class Migration_Drop_caldav_calendar_column_from_user_settings_table extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if ($this->db->field_exists('caldav_calendar', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'caldav_calendar');
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if (!$this->db->field_exists('caldav_calendar', 'user_settings')) {
            $fields = [
                'caldav_calendar' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                    'after' => 'caldav_password',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }
    }
}
