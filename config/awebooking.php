<?php
return [
    'pages_name' => [
        'home' => [
            'label' => awe_lang('Home'),
            'route' => 'home-page'
        ],
        'home_search_results' => [
            'label' => awe_lang('Search Homes'),
            'route' => 'page-search-result'
        ],
        'contact' => [
            'label' => awe_lang('Contact Us'),
            'route' => 'contact-us'
        ]
    ],
    'menu_location' => [
        'primary' => awe_lang('Primary')
    ],
    'post_types' => [
        'home' => [
            'name' => awe_lang('Home'),
            'slug' => 'home'
        ],
        'post' => [
            'name' => awe_lang('Post'),
            'slug' => 'post'
        ],
        'page' => [
            'name' => awe_lang('Page'),
            'slug' => 'page'
        ]
    ],
    'payment_gateways' => [
        'bank_transfer' => awe_lang('BankTransfer'),
        'paypal' => awe_lang('Paypal'),
        'stripe' => awe_lang('Stripe')
    ],
    'service_status' => [
        'publish' => [
            'name' => awe_lang('Publish')
        ],
        'pending' => [
            'name' => awe_lang('Pending')
        ],
        'draft' => [
            'name' => awe_lang('Draft')
        ],
        'trash' => [
            'name' => awe_lang('Trash')
        ]
    ],
    'booking_status' => [
        'pending' => [
            'label' => awe_lang('Pending'),
            'icon' => 'fe-alert-triangle',
            'payment_text' => awe_lang('Your payment has not been confirmed')
        ],
        'incomplete' => [
            'label' => awe_lang('Incomplete'),
            'icon' => 'fe-alert-circle',
            'payment_text' => awe_lang('Your payment is processing')
        ],
        'completed' => [
            'label' => awe_lang('Completed'),
            'icon' => 'fe-check-circle',
            'payment_text' => awe_lang('Your payment was successful')
        ],
        'canceled' => [
            'label' => awe_lang('Canceled'),
            'icon' => 'fe-x-circle',
            'payment_text' => awe_lang('Your payment has been canceled')
        ],
    ],
    'checkout_slug' => 'checkout',
    'after_checkout_slug' => 'thank-you',
    'prefix_dashboard' => 'dashboard',
    'prefix_auth' => 'auth',
    'key_encrypt' => 'hh',
    'date_format' => 'm-d-Y',
    'time_format' => 'H:i A',
    'media_size' => [
        'large' => [1200, 900],
        'medium' => [800, 600],
        'small' => [400, 300]
    ],
    'posts_per_page' => [
        'global' => 20,
        'blog' => 6,
        'home' => 12,
        'media' => 12,
        'term' => 12
    ],
    'comments_per_page' => [
        'blog' => 5,
        'home' => 5
    ],
    'admin_menu' => [
        [
            'type' => 'heading',
            'label' => awe_lang('General')
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Dashboard'),
            'icon' => '001_dashboard',
            'screen' => '/',
            'route' => 'dashboard'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Your Profile'),
            'icon' => '011_user_1',
            'screen' => 'profile',
            'route' => 'profile'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Notifications'),
            'icon' => '003_error',
            'screen' => 'all-notifications',
            'route' => 'all-notifications'
        ],
        [
            'type' => 'parent',
            'label' => awe_lang('Pages'),
            'icon' => '005_website',
            'child' => [
                [
                    'type' => 'item',
                    'label' => awe_lang('All Pages'),
                    'screen' => 'all-page',
                    'route' => 'all-page'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Add new Page'),
                    'screen' => 'add-new-page',
                    'route' => 'add-new-page'
                ]
            ],
            'route' => ['all-page', 'add-new-page']
        ],
        [
            'type' => 'parent',
            'label' => awe_lang('Posts'),
            'icon' => '004_post',
            'child' => [
                [
                    'type' => 'item',
                    'label' => awe_lang('All Posts'),
                    'screen' => 'all-post',
                    'route' => 'all-post'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Add new Post'),
                    'screen' => 'add-new-post',
                    'route' => 'add-new-post'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Categories'),
                    'screen' => 'post-category',
                    'route' => 'post-category'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Tags'),
                    'screen' => 'post-tag',
                    'route' => 'post-tag'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Comments'),
                    'screen' => 'comment',
                    'route' => 'comment'
                ]
            ],
            'route' => ['all-post', 'add-new-post', 'post-category', 'post-tag', 'comment']
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('All Services')
        ],
        [
            'type' => 'parent',
            'label' => awe_lang('Homes'),
            'icon' => '006_home',
            'child' => [
                [
                    'type' => 'item',
                    'label' => awe_lang('Add new Home'),
                    'screen' => 'add-new-home',
                    'route' => 'add-new-home'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('My Homes'),
                    'screen' => 'my-home',
                    'route' => 'my-home'
                ],
                [
                    'type' => 'hidden',
                    'label' => awe_lang('Edit Home'),
                    'screen' => 'edit-home',
                    'route' => 'edit-home'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Reviews'),
                    'screen' => 'home-review',
                    'route' => 'home-review'
                ]
            ],
            'route' => ['add-new-home', 'my-home', 'edit-home', 'home-review']
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('Reservation'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Reservations'),
            'icon' => '007_bars',
            'screen' => 'all-booking',
            'route' => 'all-booking'
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('System Setting'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Settings'),
            'icon' => '008_settings',
            'screen' => 'settings',
            'route' => 'settings'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Menus'),
            'icon' => '009_menu',
            'screen' => 'menus',
            'route' => 'menus'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Media'),
            'icon' => '010_gallery',
            'screen' => 'media',
            'route' => 'media'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Users'),
            'icon' => '002_user',
            'screen' => 'user-management',
            'route' => 'user-management'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Coupons'),
            'icon' => '012_voucher',
            'screen' => 'coupon',
            'route' => 'coupon'
        ],
        [
            'type' => 'parent',
            'label' => awe_lang('Attributes'),
            'icon' => '013_wifi_signal',
            'child' => [
                [
                    'type' => 'item',
                    'label' => awe_lang('Home Types'),
                    'screen' => 'home-type',
                    'route' => 'home-type'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Home Amenities'),
                    'screen' => 'home-amenity',
                    'route' => 'home-amenity'
                ],
            ],
            'route' => ['home-type', 'home-amenity']
        ],
        /*[
            'type' => 'item',
            'label' => awe_lang('Packages'),
            'icon' => '014_id_card',
            'screen' => 'package',
            'route' => 'package'
        ]*/
        [
            'type' => 'heading',
            'label' => awe_lang('Tools'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Languages'),
            'icon' => 'language',
            'screen' => 'language',
            'route' => 'language'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Translation'),
            'icon' => 'translation',
            'screen' => 'translation',
            'route' => 'translation'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Import Data'),
            'icon' => '001_download',
            'screen' => 'import-data',
            'route' => 'import-date'
        ],
    ],
    'partner_menu' => [
        [
            'type' => 'heading',
            'label' => awe_lang('General'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Dashboard'),
            'icon' => '001_dashboard',
            'screen' => '/',
            'route' => ''
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Your Profile'),
            'icon' => '011_user_1',
            'screen' => 'profile',
            'route' => 'profile'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Notifications'),
            'icon' => '003_error',
            'screen' => 'all-notifications',
            'route' => 'all-notifications'
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('All Services'),
        ],
        [
            'type' => 'parent',
            'label' => awe_lang('Homes'),
            'icon' => '006_home',
            'child' => [
                [
                    'type' => 'item',
                    'label' => awe_lang('My Homes'),
                    'screen' => 'my-home',
                    'route' => 'my-home'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Add new Home'),
                    'screen' => 'add-new-home',
                    'route' => 'add-new-home'
                ],
                [
                    'type' => 'hidden',
                    'label' => awe_lang('Edit Home'),
                    'screen' => 'edit-home',
                    'route' => 'edit-home'
                ],
                [
                    'type' => 'item',
                    'label' => awe_lang('Reviews'),
                    'screen' => 'home-review',
                    'route' => 'home-review'
                ]
            ],
            'route' => ['my-home', 'add-new-home', 'edit-home', 'home-review']
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('Reservation'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Reservations'),
            'icon' => '007_bars',
            'screen' => 'all-booking',
            'route' => 'all-booking'
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('System Setting'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Media'),
            'icon' => '010_gallery',
            'screen' => 'media',
            'route' => 'media'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Coupons'),
            'icon' => '012_voucher',
            'screen' => 'coupon',
            'route' => 'coupon'
        ]
    ],
    'customer_menu' => [
        [
            'type' => 'heading',
            'label' => awe_lang('General'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Dashboard'),
            'icon' => '001_dashboard',
            'screen' => '/',
            'route' => 'dashboard'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Your Profile'),
            'icon' => '011_user_1',
            'screen' => 'profile',
            'route' => 'profile'
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Notifications'),
            'icon' => '003_error',
            'screen' => 'all-notifications',
            'route' => 'all-notifications'
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('Booking Management'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Reservations'),
            'icon' => '007_bars',
            'screen' => 'all-booking',
            'route' => 'all-booking'
        ],
        [
            'type' => 'heading',
            'label' => awe_lang('System Setting'),
        ],
        [
            'type' => 'item',
            'label' => awe_lang('Media'),
            'icon' => '010_gallery',
            'screen' => 'media',
            'route' => 'media'
        ],
    ],
    'home_settings' => [
        'sections' => [
            [
                'id' => 'detail_options',
                'label' => awe_lang('Details'),
            ],
            [
                'id' => 'location_options',
                'label' => awe_lang('Location'),
            ],
            [
                'id' => 'pricing_options',
                'label' => awe_lang('Pricing'),
            ],
            [
                'id' => 'gallery_options',
                'label' => awe_lang('Gallery'),
                'trans' => 'none'
            ],
            [
                'id' => 'amenities_options',
                'label' => awe_lang('Amenities'),
            ],
            [
                'id' => 'policies_options',
                'label' => awe_lang('Policies'),
            ],
            [
                'id' => 'availability_options',
                'label' => awe_lang('Availability'),
                'trans' => 'none'
            ],
        ],
        'fields' => [
            [
                'id' => 'post_title',
                'label' => awe_lang('Title'),
                'type' => 'text',
                'desc' => awe_lang('Enter a minimum of 6 characters'),
                'validation' => 'required:|min:6:m',
	            'trans' => 'yes',
                'section' => 'detail_options'
            ],
            [
                'id' => 'post_slug',
                'label' => awe_lang('Permalink'),
                'type' => 'permalink',
                'post_type' => 'home',
                'section' => 'detail_options'
            ],
            [
                'id' => 'post_content',
                'label' => awe_lang('Detail'),
                'type' => 'editor',
                'trans' => 'yes',
                'section' => 'detail_options'
            ],
            [
                'id' => 'post_description',
                'label' => awe_lang('Description'),
                'type' => 'textarea',
                'trans' => 'yes',
                'section' => 'detail_options'
            ],
            [
                'id' => 'is_featured',
                'label' => awe_lang('is Featured?'),
                'type' => 'on_off',
                'permission' => ['administrator'],
                'section' => 'detail_options'
            ],
            [
                'id' => 'booking_form',
                'label' => awe_lang('Booking Form'),
                'type' => 'select',
                'choices' => [
                    'instant' => awe_lang('Instant'),
                    'enquiry' => awe_lang('Enquiry'),
                    'instant_enquiry' => awe_lang('Instant & Enquiry')
                ],
                'std' => 'instant',
                'layout' => 'col-12 col-md-6',
                'section' => 'detail_options'
            ],
            [
                'id' => 'home_type',
                'label' => awe_lang('Home Type'),
                'type' => 'select',
                'choices' => 'terms:home-type',
                'field_type' => 'taxonomy',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'style' => 'wide',
                'section' => 'detail_options'
            ],
            [
                'id' => 'number_of_guest',
                'label' => awe_lang('No. of Guest'),
                'type' => 'number',
                'minlength' => 1,
                'std' => 1,
                'validation' => 'required|integer',
                'layout' => 'col-12 col-md-6',
                'section' => 'detail_options'
            ],
            [
                'id' => 'number_of_bedrooms',
                'label' => awe_lang('No. of Bedrooms'),
                'type' => 'number',
                'minlength' => 1,
                'std' => 1,
                'validation' => 'required|integer',
                'layout' => 'col-12 col-md-6',
                'section' => 'detail_options'
            ],
            [
                'id' => 'number_of_bathrooms',
                'label' => awe_lang('No. of Bathrooms'),
                'type' => 'number',
                'minlength' => 0,
                'std' => 1,
                'validation' => 'required|integer',
                'layout' => 'col-12 col-md-6',
                'section' => 'detail_options'
            ],
            [
                'id' => 'size',
                'label' => awe_lang('Size (m2/ft)'),
                'type' => 'text',
                'std' => 0,
                'validation' => 'required',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'detail_options'
            ],
            [
                'id' => 'min_stay',
                'label' => awe_lang('Min Stay'),
                'type' => 'number',
                'minlength' => 1,
                'std' => 1,
                'validation' => 'required|integer',
                'layout' => 'col-12 col-md-6',
                'section' => 'detail_options'
            ],
            [
                'id' => 'max_stay',
                'label' => awe_lang('Max Stay'),
                'desc' => awe_lang('-1: Unlimited'),
                'type' => 'number',
                'minlength' => -1,
                'std' => -1,
                'validation' => 'required|integer',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'detail_options'
            ],
            [
                'id' => 'location',
                'label' => awe_lang('Location Settings'),
                'type' => 'location',
                'field_type' => 'location',
                'section' => 'location_options',
                'trans' => 'yes'
            ],
            [
                'id' => 'booking_type',
                'label' => awe_lang('Booking Type'),
                'type' => 'select',
                'choices' => [
                    'per_night' => awe_lang('Per Night'),
                    'per_hour' => awe_lang('Per Hour'),
                ],
                'std' => 'per_night',
                'style' => 'wide',
                'layout' => 'col-12 col-md-3',
                'break' => true,
                'section' => 'pricing_options'
            ],
            [
                'id' => 'base_price',
                'label' => awe_lang('Base Price'),
                'type' => 'text',
                'validation' => 'required',
                'layout' => 'col-12 col-md-6',
                'break' => true,
	            'trans' => 'none',
                'section' => 'pricing_options'
            ],
            [
                'id' => 'weekend_price',
                'label' => awe_lang('Weekend Price'),
                'type' => 'text',
                'desc' => awe_lang('Leave empty if it is the same with the base price'),
                'layout' => 'col-12 col-md-6',
                'section' => 'pricing_options',
                'trans' => 'none'
            ],
            [
                'id' => 'weekend_to_apply',
                'label' => awe_lang('Days to apply weekend'),
                'type' => 'select',
                'choices' => [
                    'sun' => awe_lang('Sunday'),
                    'sat_sun' => awe_lang('Saturday & Sunday'),
                    'fri_sat_sun' => awe_lang('Friday & Saturday & Sunday'),
                ],
                'std' => 'sun',
                'style' => 'wide',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'pricing_options'
            ],
            [
                'id' => 'extra_services',
                'type' => 'list_item',
                'label' => awe_lang('Extra Services'),
                'binded_from' => 'extra_services_name___',
                'items' => [
                    [
                        'id' => 'name',
                        'label' => awe_lang('Name'),
                        'type' => 'text',
                        'trans' => 'yes',
                        'layout' => 'col-12 col-md-6'
                    ],
                    [
                        'id' => 'name_unique',
                        'label' => awe_lang('ID'),
                        'type' => 'unique',
                        'binded_from' => 'extra_services_name___',
                        'layout' => 'col-12 col-md-6'
                    ],
                    [
                        'id' => 'price',
                        'label' => awe_lang('Price'),
                        'type' => 'text',
                        'trans' => 'none',
                        'layout' => 'col-12 col-md-6',
                    ],
                    [
                        'id' => 'required',
                        'label' => awe_lang('Required'),
                        'type' => 'on_off',
                        'layout' => 'col-12 col-md-6',
                        'break' => true
                    ],
                ],
                'layout' => 'col-12',
                'break' => true,
                'section' => 'pricing_options'
            ],
            [
                'id' => 'custom_price',
                'label' => awe_lang('Custom Price / Availability'),
                'desc' => awe_lang('You can change price, availability by daily, weekly, monthly, ...'),
                'type' => 'price',
                'section' => 'pricing_options'
            ],
            [
                'id' => 'amenities',
                'label' => awe_lang('Amenities'),
                'type' => 'checkbox',
                'choices' => 'terms:home-amenity',
                'field_type' => 'taxonomy',
                'style' => 'col',
                'section' => 'amenities_options'
            ],
            [
                'id' => 'gallery',
                'label' => awe_lang('Gallery'),
                'type' => 'media_advanced',
                'section' => 'gallery_options'
            ],
            [
                'id' => 'enable_cancellation',
                'label' => awe_lang('Enable Cancellation'),
                'type' => 'on_off',
                'section' => 'policies_options'
            ],
            [
                'id' => 'cancel_before',
                'label' => awe_lang('Cancel Before (days)'),
                'type' => 'number',
                'minlength' => 0,
                'validation' => 'required|numeric',
                'std' => 0,
                'condition' => 'enable_cancellation:is(on)',
                'section' => 'policies_options'
            ],
            [
                'id' => 'cancellation_detail',
                'label' => awe_lang('Cancellation Detail'),
                'type' => 'textarea',
                'trans' => 'yes',
                'condition' => 'enable_cancellation:is(on)',
                'section' => 'policies_options'
            ],
            [
                'id' => 'checkin_time',
                'type' => 'select',
                'label' => awe_lang('Check In'),
                'choices' => list_hours(30),
                'layout' => 'col-12 col-md-6',
                'style' => 'wide',
                'section' => 'policies_options'
            ],
            [
                'id' => 'checkout_time',
                'type' => 'select',
                'label' => awe_lang('Check Out'),
                'choices' => list_hours(30),
                'layout' => 'col-12 col-md-6',
                'style' => 'wide',
                'break' => true,
                'section' => 'policies_options'
            ],
            [
                'id' => 'availability',
                'label' => awe_lang('Availability'),
                'type' => 'availability',
                'excluded' => true,
                'section' => 'availability_options'
            ]
        ]
    ],
    'page_settings' => [
        'content' => [
            'fields' => [
                [
                    'id' => 'post_title',
                    'label' => awe_lang('Title'),
                    'type' => 'text',
                    'desc' => awe_lang('The title is required field'),
                    'validation' => 'required'
                ],
                [
                    'id' => 'post_slug',
                    'label' => awe_lang('Permalink'),
                    'type' => 'permalink',
                    'post_type' => 'page'
                ],
                [
                    'id' => 'post_content',
                    'label' => awe_lang('Detail'),
                    'type' => 'editor'
                ]
            ]
        ],
        'sidebar' => [
            'fields' => [
                [
                    'id' => 'thumbnail_id',
                    'label' => awe_lang('Featured Image'),
                    'type' => 'upload'
                ]
            ]
        ]
    ],
    'post_settings' => [
        'content' => [
            'fields' => [
                [
                    'id' => 'post_title',
                    'label' => awe_lang('Title'),
                    'type' => 'text',
                    'desc' => awe_lang('The title is required field'),
                    'validation' => 'required'
                ],
                [
                    'id' => 'post_slug',
                    'label' => awe_lang('Permalink'),
                    'type' => 'permalink',
                    'post_type' => 'post'
                ],
                [
                    'id' => 'post_content',
                    'label' => awe_lang('Detail'),
                    'type' => 'editor'
                ],
                [
                    'id' => 'author',
                    'label' => awe_lang('Author'),
                    'type' => 'select',
                    'choices' => 'user:administrator:0',
                ]
            ]
        ],
        'sidebar' => [
            'fields' => [
                [
                    'id' => 'post_category',
                    'label' => awe_lang('Category'),
                    'type' => 'checkbox',
                    'choices' => 'terms:post-category'
                ],
                [
                    'id' => 'post_tag',
                    'label' => awe_lang('Tags'),
                    'type' => 'tag',
                    'choices' => 'terms:post-tag'
                ],
                [
                    'id' => 'thumbnail_id',
                    'label' => awe_lang('Featured Image'),
                    'type' => 'upload'
                ]
            ]
        ]
    ],
    'theme_options' => [
        'sections' => [
            [
                'id' => 'general_options',
                'label' => awe_lang('General'),
            ],
            [
                'id' => 'page_options',
                'label' => awe_lang('Page'),
            ],
            [
                'id' => 'booking_options',
                'label' => awe_lang('Booking'),
            ],/*
            [
                'id' => 'service_options',
                'label' => awe_lang('Services'),
                'trans' => 'none'
            ],*/
            [
                'id' => 'payment_options',
                'label' => awe_lang('Payment Gateways'),
            ],
            [
                'id' => 'review_options',
                'label' => awe_lang('Reviews'),
                'trans' => 'none',
            ],
            [
                'id' => 'email_options',
                'label' => awe_lang('Email'),
            ],
            [
                'id' => 'partner_options',
                'label' => awe_lang('Partner'),
                'trans' => 'none',
            ],
            [
                'id' => 'registration',
                'label' => awe_lang('Registration'),
                'trans' => 'none'
            ],
            [
                'id' => 'footer_options',
                'label' => awe_lang('Footer'),
            ],
            [
                'id' => 'advance_options',
                'label' => awe_lang('Advanced'),
            ],
        ],
        'fields' => [
            [
                'id' => 'site_name',
                'label' => awe_lang('Site Name'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'std' => 'AweBooking',
                'break' => true,
                'section' => 'general_options'
            ],
            [
                'id' => 'site_description',
                'label' => awe_lang('Site Description'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'std' => 'Travel Booking System',
                'break' => true,
                'section' => 'general_options'
            ],
            [
                'id' => 'logo',
                'label' => awe_lang('Logo'),
                'type' => 'upload',
                'section' => 'general_options',
                'trans' => 'none'
            ],
            [
                'id' => 'dashboard_logo',
                'label' => awe_lang('Dashboard Logo'),
                'type' => 'upload',
                'layout' => 'col-12 col-md-4',
                'section' => 'general_options',
                'trans' => 'none'
            ],
            [
                'id' => 'dashboard_logo_short',
                'label' => awe_lang('Dashboard Small Logo'),
                'type' => 'upload',
                'layout' => 'col-12 col-md-4',
                'break' => true,
                'trans' => 'none',
                'section' => 'general_options'
            ],
            [
                'id' => 'favicon',
                'label' => awe_lang('Favicon'),
                'type' => 'upload',
                'section' => 'general_options',
                'trans' => 'none',
                'break' => true,
            ],
            [
                'id' => 'home_slider',
                'label' => awe_lang('Home Slider'),
                'type' => 'uploads',
                'section' => 'page_options'
            ],
            [
                'id' => 'top_destination',
                'label' => awe_lang('Top Destination'),
                'type' => 'list_item',
                'binded_from' => 'top_destination_name___',
                'items' => [
                    [
                        'id' => 'name',
                        'label' => awe_lang('Destination name'),
                        'type' => 'text',
                        'trans' => 'yes',
                    ],
                    [
                        'id' => 'lat',
                        'label' => awe_lang('Destination Lat'),
                        'type' => 'text',
                        'trans' => 'none'
                    ],
                    [
                        'id' => 'lng',
                        'label' => awe_lang('Destination Lng'),
                        'type' => 'text',
                        'trans' => 'none'
                    ],
                    [
                        'id' => 'image',
                        'label' => awe_lang('Image'),
                        'type' => 'upload',
                        'trans' => 'none'
                    ]
                ],
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'page_options'
            ],
            [
                'id' => 'testimonial',
                'label' => awe_lang('Testimonial'),
                'type' => 'list_item',
                'binded_from' => 'testimonial_author_name___',
                'items' => [
                    [
                        'id' => 'author_name',
                        'label' => awe_lang('Author Name'),
                        'type' => 'text',
                        'trans' => 'yes',
                    ],
                    [
                        'id' => 'author_avatar',
                        'label' => awe_lang('Avatar'),
                        'type' => 'upload',
                        'trans' => 'none'
                    ],
                    [
                        'id' => 'author_comment',
                        'label' => awe_lang('Comment'),
                        'type' => 'textarea',
                        'trans' => 'yes',
                    ],
                    [
                        'id' => 'author_rate',
                        'label' => awe_lang('Rate'),
                        'type' => 'range',
                        'minlength' => 1,
                        'maxlength' => [
                            'max-length' => 5
                        ],
                        'std' => 5
                    ],
                    [
                        'id' => 'date',
                        'label' => awe_lang('Created At'),
                        'type' => 'datepicker',
                        'min_date' => -1
                    ]
                ],
                'enqueue_scripts' => ['flatpickr-js', 'range-slider'],
                'enqueue_styles' => ['flatpickr-css', 'range-slider'],
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'page_options'
            ],
            [
                'id' => 'term_condition_page',
                'label' => awe_lang('Term & Condition Page'),
                'type' => 'select',
                'choices' => 'page',
                'layout' => 'col-12 col-12 col-md-6',
                'style' => 'wide',
                'break' => true,
                'section' => 'page_options'
            ],
            [
                'id' => 'call_to_action_page',
                'label' => awe_lang('Call To Action Page'),
                'type' => 'select',
                'choices' => 'page',
                'layout' => 'col-12 col-sm-6',
                'style' => 'wide',
                'break' => true,
                'section' => 'page_options'
            ],
            [
                'id' => 'blog_image',
                'label' => awe_lang('Blog page image'),
                'type' => 'upload',
                'section' => 'page_options',
                'trans' => 'none'
            ],
            [
                'id' => 'sidebar_image',
                'label' => awe_lang('Sidebar image'),
                'type' => 'upload',
                'section' => 'page_options',
                'trans' => 'none'
            ],
            [
                'id' => 'sidebar_image_link',
                'label' => awe_lang('Sidebar image link'),
                'type' => 'text',
                'section' => 'page_options',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none'
            ],
            [
                'id' => 'contact_detail',
                'label' => awe_lang('Contact Detail'),
                'type' => 'editor',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'page_options',
            ],
            [
                'id' => 'contact_map_lat',
                'label' => awe_lang('Contact Us: Map latitude'),
                'type' => 'text',
                'layout' => 'col-12 col-md-3',
                'section' => 'page_options',
                'trans' => 'none'
            ],
            [
                'id' => 'contact_map_lng',
                'label' => awe_lang('Contact Us: Map longtitude'),
                'type' => 'text',
                'layout' => 'col-12 col-md-3',
                'break' => true,
                'section' => 'page_options',
                'trans' => 'none'
            ],
            [
                'id' => 'currencies',
                'type' => 'list_item',
                'label' => awe_lang('Currencies'),
                'binded_from' => 'currencies_name___',
	            'trans' => 'yes',
                'items' => [
                    [
                        'id' => 'name',
                        'label' => awe_lang('Name'),
                        'type' => 'text',
                        'layout' => 'col-12 col-md-6',
	                    'trans' => 'yes'
                    ],
                    [
                        'id' => 'symbol',
                        'label' => awe_lang('Symbol'),
                        'type' => 'text',
                        'layout' => 'col-12 col-md-6',
                        'break' => true,
                        'trans' => 'none'
                    ],
                    [
                        'id' => 'unit',
                        'label' => awe_lang('Unit'),
                        'type' => 'select',
                        'choices' => default_currencies(),
                        'style' => 'wide',
                        'layout' => 'col-12 col-sm-4',
                    ],
                    [
                        'id' => 'exchange',
                        'label' => awe_lang('Exchange Rate'),
                        'type' => 'text',
                        'std' => 1,
                        'layout' => 'col-12 col-sm-4',
                        'trans' => 'none'
                    ],
                    [
                        'id' => 'position',
                        'label' => awe_lang('Position'),
                        'type' => 'select',
                        'choices' => [
                            'left' => awe_lang('Left ($99)'),
                            'right' => awe_lang('Right (99$)'),
                        ],
                        'style' => 'wide',
                        'std' => 'left',
                        'layout' => 'col-12 col-sm-4',
                        'break' => true,
                    ],
                    [
                        'id' => 'thousand_separator',
                        'label' => awe_lang('Thousand Separator'),
                        'type' => 'text',
                        'std' => ',',
                        'layout' => 'col-12 col-sm-4',
                        'trans' => 'none'
                    ],
                    [
                        'id' => 'decimal_separator',
                        'label' => awe_lang('Decimal Separator'),
                        'type' => 'text',
                        'std' => ',',
                        'layout' => 'col-12 col-sm-4',
                        'trans' => 'none'
                    ],
                    [
                        'id' => 'currency_decimal',
                        'label' => awe_lang('Currency Decimal'),
                        'type' => 'number',
                        'minlength' => 0,
                        'std' => 2,
                        'layout' => 'col-12 col-sm-4',
                    ],
                ],
                'std' => [
                    [
                        'name' => 'USD',
                        'symbol' => '$',
                        'unit' => 'USD',
                        'exchange' => 1,
                        'position' => 'left',
                        'thousand_separator' => ',',
                        'decimal_separator' => '.',
                        'currency_decimal' => 2
                    ]
                ],
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'booking_options'
            ],
            [
                'id' => 'primary_currency',
                'label' => awe_lang('Primary Currency'),
                'type' => 'select',
                'choices' => 'hh_currencies',
                'std' => 'USD',
                'layout' => 'col-12 col-md-6',
                'style' => 'wide',
                'break' => true,
                'section' => 'booking_options'
            ],
            [
                'id' => 'included_tax',
                'label' => awe_lang('Tax is included?'),
                'type' => 'on_off',
                'section' => 'booking_options'
            ],
            [
                'id' => 'tax',
                'label' => awe_lang('Tax (%)'),
                'type' => 'text',
                'std' => '10',
                'layout' => 'col-12 col-md-6',
                'section' => 'booking_options',
                'trans' => 'none'
            ],
            /*[
                'id' => 'service_tabs',
                'label' => awe_lang('Service Tabs'),
                'type' => 'tab',
                'tab_title' => [
                    [
                        'id' => 'home_tab',
                        'label' => awe_lang('Home'),
                    ],
                    [
                        'id' => 'experience_tab',
                        'label' => awe_lang('Experience'),
                    ],
                ],
                'tab_content' => [

                ],
                'section' => 'service_options'
            ],*/
            [
                'id' => 'payment_tabs',
                'label' => awe_lang('Payment Tabs'),
                'type' => 'payment',
                'layout' => 'col-12',
                'section' => 'payment_options'
            ],
            [
                'id' => 'enable_review',
                'label' => awe_lang('Enable Review'),
                'type' => 'on_off',
                'section' => 'review_options',
                'std' => 'on'
            ],
            [
                'id' => 'review_approval',
                'label' => awe_lang('Review approval'),
                'type' => 'on_off',
                'section' => 'review_options',
                'std' => 'on'
            ],
            [
                'id' => 'smtp_host',
                'label' => awe_lang('SMTP Host'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'email_options'
            ],
            [
                'id' => 'type_encrytion',
                'label' => awe_lang('Type of Encryption'),
                'type' => 'radio',
                'choices' => [
                    'none' => 'None',
                    'ssl' => 'SSL/TLS',
                    'tls' => 'STARTTLS'
                ],
                'std' => 'ssl',
                'section' => 'email_options'
            ],
            [
                'id' => 'smtp_port',
                'label' => awe_lang('SMTP Port'),
                'type' => 'text',
                'trans' => 'none',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'email_options'
            ],
            [
                'id' => 'smtp_username',
                'label' => awe_lang('SMTP Username'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'email_options'
            ],
            [
                'id' => 'smtp_password',
                'label' => awe_lang('SMTP Password'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'email_options'
            ],
            [
                'id' => 'send_enquire_email',
                'label' => awe_lang('Send Enquire Email'),
                'type' => 'radio',
                'choices' => [
                    'admin_customer' => awe_lang('Admin & Customer'),
                    'partner_customer' => awe_lang('Partner & Customer'),
                    'admin_partner_customer' => awe_lang('Admin, Partner & Customer')
                ],
                'std' => 'admin_partner_customer',
                'section' => 'email_options'
            ],
            [
                'id' => 'email_from_address',
                'label' => awe_lang('Email From Address'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'email_options'
            ],
            [
                'id' => 'email_from',
                'label' => awe_lang('Email From Name'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'email_options'
            ],
            [
                'id' => 'email_logo',
                'label' => awe_lang('Email Logo'),
                'type' => 'image',
                'layout' => 'col-12 col-md-6',
                'section' => 'email_options'
            ],
            [
                'id' => 'partner_commission',
                'label' => awe_lang('Commission (%)'),
                'type' => 'text',
                'std' => 10,
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'partner_options'
            ],
            [
                'id' => 'payout_date',
                'label' => awe_lang('Payout Date'),
                'desc' => awe_lang('The system will automatically payout on this date'),
                'type' => 'select',
                'choices' => 'number_range:1_31',
                'std' => date('d'),
                'style' => 'wide',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'partner_options'
            ],
            [
                'id' => 'min_balance',
                'label' => awe_lang('Minimum Balance'),
                'desc' => awe_lang('Minimum balance for the system to process payout'),
                'type' => 'text',
                'std' => 100,
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'trans' => 'none',
                'section' => 'partner_options'
            ],
            [
                'id' => 'partner_approval',
                'label' => awe_lang('Partner Approval'),
                'type' => 'on_off',
                'std' => 'on',
                'section' => 'partner_options'
            ],
            [
                'id' => 'facebook_login',
                'label' => awe_lang('Facebook Login'),
                'type' => 'on_off',
                'std' => 'off',
                'section' => 'registration'
            ],
            [
                'id' => 'facebook_api',
                'label' => awe_lang('Facebook API'),
                'type' => 'text',
                'trans' => 'none',
                'layout' => 'col-12 col-md-3',
                'condition' => 'facebook_login:is(on)',
                'section' => 'registration'
            ],
            [
                'id' => 'facebook_secret',
                'label' => awe_lang('Facebook Secret'),
                'type' => 'text',
                'layout' => 'col-12 col-md-3',
                'break' => true,
                'trans' => 'none',
                'condition' => 'facebook_login:is(on)',
                'section' => 'registration'
            ],
            [
                'id' => 'google_login',
                'label' => awe_lang('Google Login'),
                'type' => 'on_off',
                'std' => 'off',
                'section' => 'registration'
            ],
            [
                'id' => 'google_client_id',
                'label' => awe_lang('Google Client ID'),
                'type' => 'text',
                'layout' => 'col-12 col-md-3',
                'condition' => 'google_login:is(on)',
                'trans' => 'none',
                'section' => 'registration'
            ],
            [
                'id' => 'google_client_secret',
                'label' => awe_lang('Google Client Secret'),
                'type' => 'text',
                'layout' => 'col-12 col-md-3',
                'break' => true,
                'condition' => 'google_login:is(on)',
                'trans' => 'none',
                'section' => 'registration'
            ],
            //Footer
            [
                'id' => 'footer_logo',
                'label' => awe_lang('Logo Footer'),
                'type' => 'upload',
                'trans' => 'none',
                'section' => 'footer_options'
            ],
            [
                'id' => 'list_social',
                'label' => awe_lang('List Social'),
                'type' => 'list_item',
                'binded_from' => 'list_social_social_name___',
                'trans' => 'yes',
                'items' => [
                    [
                        'id' => 'social_name',
                        'label' => awe_lang('Name'),
                        'type' => 'text',
                        'trans' => 'yes',
                    ],
                    [
                        'id' => 'social_icon',
                        'label' => awe_lang('Icon'),
                        'type' => 'icon',
                    ],
                    [
                        'id' => 'social_link',
                        'label' => awe_lang('Link'),
                        'trans' => 'none',
                        'type' => 'text',
                    ]
                ],
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'footer_options'
            ],
            [
                'id' => 'footer_menu1_label',
                'label' => awe_lang('First menu label'),
                'type' => 'text',
                'layout' => 'col-12 col-md-3',
                'break' => false,
                'section' => 'footer_options'
            ],
            [
                'id' => 'footer_menu1',
                'label' => awe_lang('First menu'),
                'type' => 'select',
                'style' => 'wide',
                'choices' => 'nav',
                'section' => 'footer_options',
                'break' => true,
                'layout' => 'col-12 col-md-3',
            ],
            [
                'id' => 'footer_menu2_label',
                'label' => awe_lang('Second menu label'),
                'type' => 'text',
                'layout' => 'col-12 col-md-3',
                'break' => false,
                'section' => 'footer_options'
            ],
            [
                'id' => 'footer_menu2',
                'label' => awe_lang('Second menu'),
                'type' => 'select',
                'style' => 'wide',
                'choices' => 'nav',
                'section' => 'footer_options',
                'break' => true,
                'layout' => 'col-12 col-md-3',
            ],
            [
                'id' => 'footer_subscribe_label',
                'label' => awe_lang('Subscribe label'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'footer_options'
            ],
            [
                'id' => 'footer_subscribe_description',
                'label' => awe_lang('Subscribe description'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'footer_options'
            ],
            [
                'id' => 'copy_right',
                'label' => awe_lang('Copy right text'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'footer_options'
            ],
            //End footer
	        [
		        'id' => 'site_language',
		        'label' => awe_lang('Site Language'),
		        'type' => 'select',
		        'choices' => 'language',
		        'layout' => 'col-12 col-12 col-md-6',
		        'style' => 'wide',
		        'break' => true,
		        'section' => 'advance_options'
	        ],
	        [
		        'id' => 'multi_language',
		        'label' => awe_lang('Enable multi language'),
		        'type' => 'on_off',
		        'std'  => 'off',
		        'section' => 'advance_options',
	        ],
            [
                'id' => 'unit_of_measure',
                'label' => awe_lang('Unit Of Measure'),
                'type' => 'select',
                'choices' => [
                    'm2' => 'm2',
                    'ft2' => 'ft2'
                ],
                'std' => 'm2',
                'layout' => 'col-12 col-md-6',
                'style' => 'wide',
                'break' => true,
                'section' => 'advance_options'
            ],
            [
                'id' => 'mailchimp_api_key',
                'label' => awe_lang('MailChimp API Key'),
                'desc' => awe_lang('This key to connect to MailChimp.'),
                'type' => 'text',
                'trans' => 'none',
                'layout' => 'col-6 col-md-3',
                'section' => 'advance_options'
            ],
            [
                'id' => 'mailchimp_list',
                'label' => awe_lang('MailChimp List ID'),
                'desc' => awe_lang('The ID of the list you want to add the user to'),
                'type' => 'text',
                'trans' => 'none',
                'layout' => 'col-6 col-md-3',
                'break' => true,
                'section' => 'advance_options'
            ],
            [
                'id' => 'mapbox_key',
                'label' => awe_lang('Mapbox Key'),
                'desc' => awe_lang('Use this key to enable Mapbox map'),
                'type' => 'text',
                'trans' => 'none',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'advance_options'
            ],
            [
                'id' => 'search_radius',
                'label' => awe_lang('Search Radius'),
                'desc' => awe_lang('Search radius to find home by lat/lng'),
                'type' => 'range',
                'layout' => 'col-12 col-md-6',
                'break' => true,
                'section' => 'advance_options',
                'minlength' => 1,
                'maxlength' => [
                    'max-length' => 500
                ]
            ],
            [
                'id' => 'use_google_captcha',
                'label' => awe_lang('Use Google Captcha?'),
                'desc' => awe_lang('Use Google Captcha for checkout form, review form, contact us form, ...'),
                'type' => 'on_off',
                'section' => 'advance_options',
            ],
            [
                'id' => 'google_captcha_site_key',
                'label' => awe_lang('Google Captcha Key'),
                'type' => 'text',
                'condition' => 'use_google_captcha:is(on)',
                'layout' => 'col-6 col-md-3',
                'section' => 'advance_options'
            ],
            [
                'id' => 'google_captcha_secret_key',
                'label' => awe_lang('Google Captcha Secret'),
                'type' => 'text',
                'condition' => 'use_google_captcha:is(on)',
                'layout' => 'col-6 col-md-3',
                'break' => true,
                'section' => 'advance_options'
            ],
            [
                'id' => 'user_admin',
                'label' => awe_lang('Admin User'),
                'desc' => awe_lang('Choose an account to set as Administrator'),
                'type' => 'select',
                'style' => 'wide',
                'choices' => 'user:administrator',
                'section' => 'advance_options',
                'break' => true,
                'layout' => 'col-12 col-md-6',
            ],
            [
                'id' => 'featured_text',
                'label' => awe_lang('Featured Label'),
                'desc' => awe_lang('Setup featured label for home featured item'),
                'type' => 'text',
                'layout' => 'col-12 col-md-6',
                'std' => 'Featured',
                'break' => true,
                'section' => 'advance_options'
            ],
            [
                'id' => 'use_ssl',
                'label' => awe_lang('Enable SSL'),
                'desc' => awe_lang('The page needs to reload to be applied'),
                'type' => 'on_off',
                'section' => 'advance_options'
            ]
        ]
    ],
];
