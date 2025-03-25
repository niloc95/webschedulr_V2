<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr - Online Appointments
 * @author      N N.Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

class Migration_Revert_rename_id_service_categories_column_of_services_table extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if ($this->db->field_exists('id_categories', 'services')) {
            $this->db->query(
                'ALTER TABLE `' . $this->db->dbprefix('services') . '` DROP FOREIGN KEY `services_categories`',
            );

            $fields = [
                'id_categories' => [
                    'name' => 'id_service_categories',
                    'type' => 'INT',
                    'constraint' => '11',
                ],
            ];

            $this->dbforge->modify_column('services', $fields);

            $this->db->query(
                '
                ALTER TABLE `' .
                    $this->db->dbprefix('services') .
                    '`
                    ADD CONSTRAINT `services_service_categories` FOREIGN KEY (`id_service_categories`) REFERENCES `' .
                    $this->db->dbprefix('service_categories') .
                    '` (`id`)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE
            ',
            );
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('id_service_categories', 'services')) {
            $this->db->query(
                'ALTER TABLE `' . $this->db->dbprefix('services') . '` DROP FOREIGN KEY `services_service_categories`',
            );

            $fields = [
                'id_service_categories' => [
                    'name' => 'id_categories',
                    'type' => 'INT',
                    'constraint' => '11',
                ],
            ];

            $this->dbforge->modify_column('services', $fields);

            $this->db->query(
                '
                ALTER TABLE `' .
                    $this->db->dbprefix('services') .
                    '`
                    ADD CONSTRAINT `services_categories` FOREIGN KEY (`id_categories`) REFERENCES `' .
                    $this->db->dbprefix('service_categories') .
                    '` (`id`)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE
            ',
            );
        }
    }
}
