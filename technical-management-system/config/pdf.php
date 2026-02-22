<?php

/**
 * PDF Generation Configuration
 * 
 * Configuration for PDF coordinate overlay using FPDI
 */

return [
    /*
    |--------------------------------------------------------------------------
    | PDF Debug Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, draws a grid on the PDF to help calibrate coordinates
    |
    */
    'debug' => env('PDF_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | PDF Template Paths
    |--------------------------------------------------------------------------
    |
    | Base paths for PDF templates
    |
    */
    'templates' => [
        'base_path' => storage_path('app/templates'),
        'customer_request_form' => 'GEI-MAR-F-3 Customer Request Form Rev 2.pdf',
    ],

    /*
    |--------------------------------------------------------------------------
    | Generated PDF Storage
    |--------------------------------------------------------------------------
    |
    | Directory where generated PDFs are stored
    |
    */
    'output' => [
        'directory' => 'generated',
        'permissions' => 0644,
    ],

    /*
    |--------------------------------------------------------------------------
    | Coordinate Mapping for Customer Request Form
    |--------------------------------------------------------------------------
    |
    | These coordinates define where text will be overlaid on the template.
    | All measurements are in millimeters from the top-left corner.
    |
    | To calibrate:
    | 1. Enable PDF_DEBUG=true in .env to see grid overlay
    | 2. Generate a test PDF and measure positions
    | 3. Update x/y coordinates below
    |
    */
    'coordinates' => [
        'customer_request_form' => [
            // Company Name field
            'company_name' => [
                'page' => 1,
                'x' => 150,     // Adjust based on template
                'y' => 69,      // Adjust based on template
                'font' => 'Helvetica',
                'size' => 10,
            ],
            
            // Contact Person field
            'contact_person' => [
                'page' => 1,
                'x' => 150,
                'y' => 89,
                'font' => 'Helvetica',
                'size' => 10,
            ],
            
            // Contact Number field
            'contact_no' => [
                'page' => 1,
                'x' => 150,
                'y' => 94,
                'font' => 'Helvetica',
                'size' => 10,
            ],
            
            // Service Type field
            'service_type' => [
                'page' => 1,
                'x' => 150,
                'y' => 143,
                'font' => 'Helvetica',
                'size' => 10,
            ],
            
            // Service Description field
            'service_description' => [
                'page' => 1,
                'x' => 30,
                'y' => 150,
                'font' => 'Helvetica',
                'size' => 9,
            ],
            
            // Request Date field
            'request_date' => [
                'page' => 1,
                'x' => 250,
                'y' => 94,
                'font' => 'Helvetica',
                'size' => 10,
            ],
            
            // Job Order Number field
            'job_order_number' => [
                'page' => 1,
                'x' => 30,
                'y' => 30,
                'font' => 'Helvetica',
                'size' => 12,
            ],
        ],
    ],
];
