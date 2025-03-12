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

class Migration_Create_appointment_location_column extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('location', 'appointments')) {
            $fields = [
                'location' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'end_datetime',
                ],
            ];

            $this->dbforge->add_column('appointments', $fields);
        }

        if (!$this->db->field_exists('location', 'services')) {
            $fields = [
                'location' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'description',
                ],
            ];

            $this->dbforge->add_column('services', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('location', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'location');
        }

        if ($this->db->field_exists('location', 'services')) {
            $this->dbforge->drop_column('services', 'location');
        }
    }
}
