<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Open Source Web Scheduler
 *
 * @package     @webSchedulr
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) 2013 - 2020, Nilesh Cara
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://@webSchedulr.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_id_caldav_calendar_column_to_appointments_table extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('id_caldav_calendar', 'appointments')) {
            $fields = [
                'id_caldav_calendar' => [
                    'type' => 'TEXT',
                    'null' => null,
                    'after' => 'id_google_calendar',
                ],
            ];

            $this->dbforge->add_column('appointments', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('id_caldav_calendar', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'id_caldav_calendar');
        }
    }
}
