<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reference Number Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the format and prefix for working paper reference numbers.
    | Format: {prefix}-{year}-{sequence}
    | Example: WP-2024-00001
    |
    */

    'reference_prefix' => env('WORKING_PAPER_REFERENCE_PREFIX', 'WP'),

    /*
    |--------------------------------------------------------------------------
    | Access Token Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how long guest access tokens remain valid.
    | Tokens allow clients to view working papers without authentication.
    |
    */

    'token_expiry_days' => env('WORKING_PAPER_TOKEN_EXPIRY_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure file upload limits and allowed file types.
    |
    */

    'max_file_upload_size' => env('MAX_FILE_UPLOAD_SIZE', 10240), // in KB (10MB)

    'allowed_file_extensions' => env('ALLOWED_FILE_EXTENSIONS', 'pdf,jpg,jpeg,png,doc,docx,xls,xlsx'),

    /*
    |--------------------------------------------------------------------------
    | GST Configuration
    |--------------------------------------------------------------------------
    |
    | Configure GST rate and calculation settings.
    |
    */

    'gst_rate' => 0.10, // 10% GST rate

    'gst_rounding_tolerance' => 0.01, // 1 cent tolerance for rounding

    /*
    |--------------------------------------------------------------------------
    | Work Type Configuration
    |--------------------------------------------------------------------------
    |
    | Define available work types and their characteristics.
    |
    */

    'work_types' => [
        'wage' => [
            'label' => 'Wage',
            'requires_gst' => false,
            'requires_quarterly' => false,
            'requires_field_type' => false,
            'has_income' => false,
        ],
        'rental_property' => [
            'label' => 'Rental Property',
            'requires_gst' => false, // Optional for rental
            'requires_quarterly' => false,
            'requires_field_type' => false,
            'has_income' => true,
            'supports_multiple' => true, // Multiple properties
        ],
        'sole_trader' => [
            'label' => 'Sole Trader',
            'requires_gst' => true,
            'requires_quarterly' => true,
            'requires_field_type' => true,
            'has_income' => true,
        ],
        'bas' => [
            'label' => 'BAS',
            'requires_gst' => true,
            'requires_quarterly' => true,
            'requires_field_type' => true,
            'has_income' => true,
            'supports_consolidation' => true, // Q1-Q4 combining
        ],
        'ctax' => [
            'label' => 'Company Tax',
            'requires_gst' => true,
            'requires_quarterly' => true,
            'requires_field_type' => true,
            'has_income' => true,
        ],
        'ttax' => [
            'label' => 'Trust Tax',
            'requires_gst' => true,
            'requires_quarterly' => true,
            'requires_field_type' => true,
            'has_income' => true,
        ],
        'smsf' => [
            'label' => 'SMSF',
            'requires_gst' => true,
            'requires_quarterly' => true,
            'requires_field_type' => true,
            'has_income' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    |
    | Available status options for working papers.
    |
    */

    'statuses' => [
        'draft' => 'Draft',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'archived' => 'Archived',
    ],

];
