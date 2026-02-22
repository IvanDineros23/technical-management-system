<?php

/**
 * PDF Form Filling Configuration
 * 
 * Configuration for AcroForm PDF form filling using pdftk
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Form Filling
    |--------------------------------------------------------------------------
    |
    | Whether to enable automatic PDF form filling for service requests
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Template Paths
    |--------------------------------------------------------------------------
    |
    | Path to the fillable PDF template (AcroForm)
    |
    */
    'template_path' => storage_path('app/templates/GEI-MAR-F-3 Customer Request Form Rev 2.pdf'),

    /*
    |--------------------------------------------------------------------------
    | pdftk Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for pdftk execution
    |
    */
    'pdftk' => [
        'command' => 'pdftk',
        'enabled' => true,
        'flatten' => true,  // Flatten the PDF after filling (makes it read-only)
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Mappings
    |--------------------------------------------------------------------------
    |
    | Map JobOrder model fields to PDF form field names
    | Use the DumpPdfFieldsCommand to discover actual field names
    |
    | Format: 'model_field' => 'pdf_field_name'
    |
    */
    'field_mapping' => [
        // Customer Information
        'company_name' => 'Company Name',               // $jobOrder->customer->name or business_name
        'address1' => 'Address 1',                      // $jobOrder->customer->address
        'address2' => 'Address 2',                      // $jobOrder->customer->city, state, postal_code
        'company_tin' => 'Company TIN',                 // $jobOrder->customer->tax_id
        'contact_person' => 'Contact Person',           // $jobOrder->customer->contact_person
        'email_address' => 'Email Address',             // $jobOrder->customer->email
        'contact_number' => 'Contact Number',           // $jobOrder->customer->phone
        'date' => 'Date',                               // $jobOrder->request_date
        
        // Service Location
        'calibration_site_address1' => 'Calibration Site Address 1', // $jobOrder->service_address
        'calibration_site_address2' => 'Calibration Site Address 2', // $jobOrder->city, province
        
        // Equipment rows (8 rows available)
        // Equipment information will be populated from JobOrderItems
        
        // Additional fields
        'others' => 'Others',                           // Special service type if needed
        'remarks' => 'REMARKS',                         // $jobOrder->notes
        
        // For approval signatures (if needed)
        'name_signature' => 'Name and Signature',       // $jobOrder->requested_by
        'date_2' => 'Date_2',                           // Date field 2
    ],

    /*
    |--------------------------------------------------------------------------
    | Output Settings
    |--------------------------------------------------------------------------
    |
    | Where filled PDFs are stored
    |
    */
    'output' => [
        'directory' => 'generated',  // Relative to storage/app/public
        'permissions' => 0644,
    ],

    /*
    |--------------------------------------------------------------------------
    | FDF (Forms Data Format) Settings
    |--------------------------------------------------------------------------
    |
    | Settings for FDF file generation (used by pdftk)
    | FDF is a text format for transmitting data to/from PDF forms
    |
    */
    'fdf' => [
        'encoding' => 'UTF-8',
    ],
];
