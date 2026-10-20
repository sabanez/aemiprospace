<?php

if (!class_exists('AEMIReorderShippingFields')) {
    class AEMIReorderShippingFields
    {
        public function __construct()
        {
            add_filter('woocommerce_shipping_fields', [$this, 'reorder_shipping_fields'], 99999, 1);
            add_filter('woocommerce_billing_fields', [$this, 'reorder_billing_fields'], 99999, 1);
        }

        public function reorder_shipping_fields($fields)
        {
            unset($fields['shipping_address_2']['label_class']);
            unset($fields['shipping_address_2']['class']['1']);
            $fields['shipping_address_2']['priority'] = 21;
            $reorder_list = ['shipping_first_name', 'shipping_last_name', 'shipping_address_2'];
            $new_order = [];

            $loop = 5;

            foreach ($reorder_list as $index) {
                if (isset($fields[$index]) && $fields[$index]) {
                    $new_order[$index] = $fields[$index];
                    $new_order[$index]['priority'] = $loop;
                    $loop += 5;
                    unset($fields[$index]);
                }
            }

            $fields = array_merge($new_order, $fields);
            $groupTwoColumns = false;
            foreach ($fields as $key => $value) {
                if ($key == 'shipping_company') {
                    $groupTwoColumns = true;
                }

                if ($groupTwoColumns) {
                    if (($indexClassExist = array_search('form-row-wide', $value['class'])) !== false) {
                        unset($fields[$key]['class'][$indexClassExist]);
                    }

                    $fields[$key]['class'][] = 'form-row-33';
                }
            }

            return $fields;
        }

        public function reorder_billing_fields($fields)
        {
            unset($fields['billing_address_2']['label_class']);
            unset($fields['billing_address_2']['class']['1']);
            $fields['billing_address_2']['priority'] = 21;
            $reorder_list = ['billing_first_name', 'billing_last_name', 'billing_address_2', 'billing_email', 'billing_phone'];
            $new_order = [];

            $loop = 5;

            foreach ($reorder_list as $index) {
                if (isset($fields[$index]) && $fields[$index]) {
                    $new_order[$index] = $fields[$index];
                    $new_order[$index]['priority'] = $loop;
                    $loop += 5;
                    unset($fields[$index]);
                }
            }

            $fields = array_merge($new_order, $fields);
            $groupTwoColumns = false;
            foreach ($fields as $key => $value) {
                if ($key == 'billing_company') {
                    $groupTwoColumns = true;
                }

                if ($groupTwoColumns) {
                    if (($indexClassExist = array_search('form-row-wide', $value['class'])) !== false) {
                        unset($fields[$key]['class'][$indexClassExist]);
                    }

                    $fields[$key]['class'][] = 'form-row-33';
                }
            }

            return $fields;
        }
    }

    new AEMIReorderShippingFields();
}
