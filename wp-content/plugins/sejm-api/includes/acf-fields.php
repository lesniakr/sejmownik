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
                    // Basic Identification Tab
                    array(
                        'key' => 'field_mp_basic_tab',
                        'label' => 'Podstawowe dane',
                        'name' => '',
                        'type' => 'tab',
                        'placement' => 'top',
                    ),
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
                        'key' => 'field_mp_active',
                        'label' => 'Aktywny',
                        'name' => 'active',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                        'instructions' => 'Czy poseł jest aktywny w obecnej kadencji',
                    ),
                    
                    // Parliamentary Information Tab
                    array(
                        'key' => 'field_mp_parliament_tab',
                        'label' => 'Informacje parlamentarne',
                        'name' => '',
                        'type' => 'tab',
                        'placement' => 'top',
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
                        'key' => 'field_mp_voivodeship',
                        'label' => 'Województwo',
                        'name' => 'voivodeship',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_number_of_votes',
                        'label' => 'Liczba głosów',
                        'name' => 'numberOfVotes',
                        'type' => 'number',
                        'instructions' => 'Liczba głosów otrzymanych w wyborach',
                    ),
                    
                    // Personal Information Tab
                    array(
                        'key' => 'field_mp_personal_tab',
                        'label' => 'Informacje osobiste',
                        'name' => '',
                        'type' => 'tab',
                        'placement' => 'top',
                    ),
                    array(
                        'key' => 'field_mp_birth_date',
                        'label' => 'Data urodzenia',
                        'name' => 'birthDate',
                        'type' => 'date_picker',
                        'display_format' => 'd.m.Y',
                        'return_format' => 'Y-m-d',
                    ),
                    array(
                        'key' => 'field_mp_birth_location',
                        'label' => 'Miejsce urodzenia',
                        'name' => 'birthLocation',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_education',
                        'label' => 'Wykształcenie',
                        'name' => 'educationLevel',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_mp_profession',
                        'label' => 'Zawód',
                        'name' => 'profession',
                        'type' => 'text',
                    ),
                    
                    // Contact Information Tab
                    array(
                        'key' => 'field_mp_contact_tab',
                        'label' => 'Dane kontaktowe',
                        'name' => '',
                        'type' => 'tab',
                        'placement' => 'top',
                    ),
                    array(
                        'key' => 'field_mp_email',
                        'label' => 'Adres e-mail',
                        'name' => 'email',
                        'type' => 'email',
                    ),
                    
                    // Additional Information Tab
                    array(
                        'key' => 'field_mp_additional_tab',
                        'label' => 'Dodatkowe informacje',
                        'name' => '',
                        'type' => 'tab',
                        'placement' => 'top',
                    ),
                    array(
                        'key' => 'field_mp_bio',
                        'label' => 'Biografia',
                        'name' => 'biography',
                        'type' => 'wysiwyg',
                    ),
                    array(
                        'key' => 'field_mp_accusative',
                        'label' => 'Forma w bierniku',
                        'name' => 'accusativeName',
                        'type' => 'text',
                        'instructions' => 'Imię i nazwisko w formie biernika (kogo? co?)',
                    ),
                    array(
                        'key' => 'field_mp_genitive',
                        'label' => 'Forma w dopełniaczu',
                        'name' => 'genitiveName',
                        'type' => 'text',
                        'instructions' => 'Imię i nazwisko w formie dopełniacza (kogo? czego?)',
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
