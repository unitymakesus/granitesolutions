<?php
/**
 * Selfie Wall
 */
class CbbSelfieWallModule extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __('Selfie Wall', 'cbb' ),
            'description' => __('A honeycomb grid of team members.', 'cbb'),
            'category'    => __('Basic', 'cbb'),
            'dir'         => CBB_MODULES_DIR . 'modules/cbb-selfie-wall/',
            'url'         => CBB_MODULES_URL . 'modules/cbb-selfie-wall/'
        ]);
    }
}

/**
 * Register the module
 */
FLBuilder::register_module('CbbSelfieWallModule', [
    'cbb-selfie-wall-items' => [
        'title'    => __('Selfies', 'cbb'),
        'sections' => [
            'general' => [
                'title'  => '',
                'fields' => [
                    'items' => [
                        'type'         => 'form',
                        'label'        => __('Selfie', 'cbb'),
                        'form'         => 'selfie_wall_items_form',
                        'preview_text' => 'label',
                        'multiple'     => true,
                    ],
                ],
            ],
        ],
    ],
]);

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form('selfie_wall_items_form', [
    'title' => __('Add Selfie', 'cbb'),
    'tabs'  => [
        'general' => [
            'title'    => __('General', 'cbb'),
            'sections' => [
                'general' => [
                    'title'  => '',
                    'fields' => [
                        'label' => [
                            'type'        => 'text',
                            'description' => __('Add the person’s full name here for administrative purposes.', 'cbb'),
                            'label'       => __('Label', 'cbb'),
                            'connections' => ['string'],
                        ],
                    ],
                ],
                'content' => [
                    'title'  => __('Content', 'cbb'),
                    'fields' => [
                        'first_name' => [
                            'type'        => 'text',
                            'label'       => __('First Name', 'cbb'),
                        ],
                        'job_role' => [
                            'type'        => 'text',
                            'label'       => __('Job / Role', 'cbb'),
                        ],
                        'location' => [
                            'type'        => 'text',
                            'label'       => __('Location', 'cbb'),
                        ],
                        'image' => [
                            'type' => 'photo',
                            'label' => __('Image', 'cbb'),
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
