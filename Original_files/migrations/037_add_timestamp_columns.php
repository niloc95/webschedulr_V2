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

class Migration_Add_timestamp_columns extends WS_Migration
{
    /**
     * @var string[]
     */
    protected $tables = ['appointments', 'categories', 'consents', 'roles', 'services', 'settings', 'users'];

    /**
     * @var string[]
     */
    protected $columns = ['delete_datetime', 'update_datetime', 'create_datetime'];

    /**
     * Upgrade method.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            foreach ($this->columns as $column) {
                if (!$this->db->field_exists($column, $table)) {
                    $fields = [
                        $column => [
                            'type' => 'DATETIME',
                            'null' => true,
                            'after' => 'id',
                        ],
                    ];

                    $this->dbforge->add_column($table, $fields);
                }
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            foreach ($this->columns as $column) {
                if ($this->db->field_exists($column, $table)) {
                    $this->dbforge->drop_column($table, $column);
                }
            }
        }
    }
}
