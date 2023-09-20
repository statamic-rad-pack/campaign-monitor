<?php

return [

    'api_key' => env('CAMPAIGNMONITOR_API_KEY'),
    
    'client_id' => env('CAMPAIGNMONITOR_CLIENT_ID'),

    /*
     * Set to `true` to add new user registrations to a Campaign Monitor list
     */
    'add_new_users' => false,

    'users' => [
        /*
        * A Campaign Monitor List ID.
        *
        */
        'list_id' => null,

        /*
        * Set to `true` to require consent before subscribing someone
        * Default: `true`
        */
        'check_consent' => true,

        /*
        * Field name used to check for consent.
        * Default: 'consent'
        */
        'consent_field' => 'consent',
        
        /*
        * Set to `true` to require consent before SMS subscribing someone
        * Default: `true`
        */
        'check_consent_sms' => true,

        /*
        * Field name used to check for consent.
        * Default: 'consent_sms'
        */
        'consent_field_sms' => 'consent_sms',

        /*
        * Store information about your contacts with custom fields.
        */
        'custom_fields' => [
            // [
            //     /*
            //     * The Campaign Monitor key
            //     */
            //     'key'=> null,

            //     /*
            //     * Blueprint field name to use for the merge field
            //     */
            //     'field_name' => null,
            // ],
        ],
        
        /*
        * Field that contains the mobile number
        * Default: 'mobile'
        */
        'mobile_field' => 'mobile',
        
        /*
        * Field that contains the name
        * Default: 'name'
        */
        'name_field' => 'name',
        
        /*
        * Field that contains the primary email address
        * Default: 'email'
        */
        'primary_email_field' => 'email',
    ],

    /*
     * The form submissions to add to your Campaign Monitor lists
     */
    'forms' => [],
];
