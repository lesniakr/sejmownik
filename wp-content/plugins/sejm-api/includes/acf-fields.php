<?php
/**
 * Advanced Custom Fields for Members of Parliament
 */

class MP_ACF {
    /**
     * Register ACF fields
     */
    public static function register_fields() {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_mp_details',
                'title' => 'Szczegóły Posła',
                'fields' => array(
                    array(
                        'key' => 'field_mp_id',
                        'label' => 'ID posła',
                        'name' => 'mp_id',
                        'type' => 'text',
                        'readonly' => 1,
                    ),
                    array(
                        'key' => 'field_mp_first_name',
                        'label' => 'Imię',
                        'name' => 'first_name',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_last_name',
                        'label' => 'Nazwisko',
                        'name' => 'last_name',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_club',
                        'label' => 'Klub/Partia',
                        'name' => 'club',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_district',
                        'label' => 'Okręg wyborczy',
                        'name' => 'district',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_district_number',
                        'label' => 'Numer okręgu',
                        'name' => 'district_number',
                        'type' => 'number',
                    ),
                    array(
                        'key' => 'field_mp_email',
                        'label' => 'Adres e-mail',
                        'name' => 'email',
                        'type' => 'email',
                    ),
                    array(
                        'key' => 'field_mp_voivodeship',
                        'label' => 'Województwo',
                        'name' => 'voivodeship',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_bio',
                        'label' => 'Biografia',
                        'name' => 'biography',
                        'type' => 'wysiwyg',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'mp',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
            ));
        }
    }
}
