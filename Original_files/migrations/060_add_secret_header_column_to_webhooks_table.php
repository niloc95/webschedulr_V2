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

class Migration_Add_secret_header_column_to_webhooks_table extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('secret_header', 'webhooks')) {
            $fields = [
                'secret_header' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'default' => 'X-Ea-Token',
                    'after' => 'actions',
                ],
            ];

            $this->dbforge->add_column('webhooks', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('secret_header', 'webhooks')) {
            $this->dbforge->drop_column('webhooks', 'secret_header');
        }
    }
}
