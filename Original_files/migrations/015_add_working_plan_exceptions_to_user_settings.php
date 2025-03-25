<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilesh Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_working_plan_exceptions_to_user_settings extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('working_plan_exceptions', 'user_settings')) {
            $fields = [
                'working_plan_exceptions' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'working_plan',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('working_plan_exceptions', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'working_plan_exceptions');
        }
    }
}
