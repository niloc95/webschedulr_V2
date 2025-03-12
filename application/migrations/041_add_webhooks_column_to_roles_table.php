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

class Migration_Add_webhooks_column_to_roles_table extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('webhooks', 'roles')) {
            $fields = [
                'webhooks' => [
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => true,
                ],
            ];

            $this->dbforge->add_column('roles', $fields);

            $this->db->update('roles', ['webhooks' => '15'], ['slug' => 'admin']);

            $this->db->update('roles', ['webhooks' => '0'], ['slug !=' => 'admin']);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('webhooks', 'roles')) {
            $this->dbforge->drop_column('roles', 'webhooks');
        }
    }
}
