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

class Migration_Add_ldap_rows_to_settings_table extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        $now = date('Y-m-d H:i:s');

        $timestamps = [
            'create_datetime' => $now,
            'update_datetime' => $now,
        ];

        if (!$this->db->get_where('settings', ['name' => 'ldap_is_active'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_is_active',
                'value' => '0',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'ldap_host'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_host',
                'value' => '',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'ldap_port'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_port',
                'value' => '',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'ldap_user_dn'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_user_dn',
                'value' => '',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'ldap_password'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_password',
                'value' => '',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'ldap_base_dn'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_base_dn',
                'value' => '',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'ldap_filter'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_filter',
                'value' => LDAP_DEFAULT_FILTER,
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'ldap_field_mapping'])->num_rows()) {
            $this->db->insert('settings', [
                'create_datetime' => $timestamps['create_datetime'],
                'update_datetime' => $timestamps['update_datetime'],
                'name' => 'ldap_field_mapping',
                'value' => json_encode(LDAP_DEFAULT_FIELD_MAPPING, JSON_PRETTY_PRINT),
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->get_where('settings', ['name' => 'ldap_is_active'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_is_active']);
        }

        if ($this->db->get_where('settings', ['name' => 'ldap_host'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_host']);
        }

        if ($this->db->get_where('settings', ['name' => 'ldap_port'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_port']);
        }

        if ($this->db->get_where('settings', ['name' => 'ldap_user_dn'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_user_dn']);
        }

        if ($this->db->get_where('settings', ['name' => 'ldap_password'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_password']);
        }

        if ($this->db->get_where('settings', ['name' => 'ldap_base_dn'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_base_dn']);
        }

        if ($this->db->get_where('settings', ['name' => 'ldap_filter'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_filter']);
        }

        if ($this->db->get_where('settings', ['name' => 'ldap_field_mapping'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'ldap_field_mapping']);
        }
    }
}
