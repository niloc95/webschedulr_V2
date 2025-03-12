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

/**
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Add_future_booking_limit_setting extends CI_Migration
{
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up(): void
    {
        if (!$this->db->get_where('settings', ['name' => 'future_booking_limit'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'future_booking_limit',
                'value' => '90', // days
            ]);
        }
    }

    /**
     * Downgrade method.
     *
     * @throws Exception
     */
    public function down(): void
    {
        if ($this->db->get_where('settings', ['name' => 'future_booking_limit'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'future_booking_limit']);
        }
    }
}
