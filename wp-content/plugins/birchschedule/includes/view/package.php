<?php

birch_ns( 'birchschedule.view', function( $ns ) {

        $_ns_data = new stdClass();

        birch_defn( $ns, 'init', function() use ( $ns, $_ns_data ) {
            
                global $birchschedule;

                $_ns_data->page_hooks = array();

                add_action( 'init', array( $ns, 'wp_init' ) );

                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

                add_action( 'admin_menu', array( $ns, 'create_admin_menus' ) );

                add_action( 'custom_menu_order', array( $ns, 'if_change_custom_menu_order' ), 100 );

                add_action( 'menu_order', array( $ns, 'change_admin_menu_order' ), 100 );

                add_action( 'plugins_loaded', array( $ns, 'load_i18n' ) );

                add_action( 'birchpress_view_load_post_edit_after', function( $arg ) use ( $ns ) {
                        $ns->load_page_edit( $arg );
                        $ns->load_post_edit( $arg );
                    } );

                add_action( 'birchpress_view_load_post_new_after', function( $arg ) use ( $ns ) {
                        $ns->load_page_edit( $arg );
                        $ns->load_post_new( $arg );
                    } );

                add_action( 'birchpress_view_enqueue_scripts_post_new_after', function( $arg ) use ( $ns ) {
                        $ns->enqueue_scripts_post_new( $arg );
                        $ns->enqueue_scripts_edit( $arg );
                    } );

                add_action( 'birchpress_view_enqueue_scripts_post_edit_after', function( $arg ) use ( $ns ) {
                        $ns->enqueue_scripts_post_edit( $arg );
                        $ns->enqueue_scripts_edit( $arg );
                    } );

                add_action( 'birchpress_view_enqueue_scripts_post_list_after', function( $arg ) use ( $ns ) {
                        $ns->enqueue_scripts_list( $arg );
                    } );

                add_action( 'birchpress_view_save_post_after', function( $post_a ) use ( $ns ) {
                        $ns->save_post( $post_a );
                    } );

                add_filter( 'birchpress_view_pre_save_post',
                    function( $post_data, $post_data2, $post_attr ) use ( $ns ) {
                        return $ns->pre_save_post( $post_data, $post_attr );
                    }, 20, 3 );
            } );

        birch_defn( $ns, 'wp_init', function() use ( $ns ) {
                if ( !defined( 'DOING_AJAX' ) ) {
                    $ns->register_common_scripts();
                    $ns->register_common_styles();
                    $ns->register_common_scripts_data_fns();
                }
            } );

        birch_defn( $ns, 'wp_admin_init', function() {} );

        birch_defn( $ns, 'get_post_type_lookup_config', function() {
                return array(
                    'key' => 'post_type',
                    'lookup_table' => array()
                );
            } );

        birch_defmulti( $ns, 'enqueue_scripts_post_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'enqueue_scripts_post_new', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'enqueue_scripts_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'enqueue_scripts_list', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'load_page_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'load_post_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'load_post_new', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'save_post', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'pre_save_post', $ns->get_post_type_lookup_config, function( $post_data, $post_attr ) {
                return $post_data;
            } );

        birch_defn( $ns, 'get_current_post_type', function() {
                global $birchpress;

                return $birchpress->view->get_current_post_type();
            } );

        birch_defn( $ns, 'enqueue_scripts', function( $scripts ) {
                global $birchpress;

                $birchpress->view->enqueue_scripts( $scripts );
            } );

        birch_defn( $ns, 'enqueue_styles', function( $styles ) {
                global $birchpress;

                $birchpress->view->enqueue_styles( $styles );
            } );

        birch_defn( $ns, 'merge_request', function( $model, $config, $request ) {
                global $birchschedule;

                return $birchschedule->model->merge_data( $model, $config, $request );
            } );

        birch_defn( $ns, 'apply_currency_to_label', function( $label, $currency_code ) {
                global $birchpress, $birchschedule;

                $currencies = $birchpress->util->get_currencies();
                $currency = $currencies[$currency_code];
                $symbol = $currency['symbol_right'];
                if ( $symbol == '' ) {
                    $symbol = $currency['symbol_left'];
                }
                return $label = $label . ' (' . $symbol . ')';
            } );

        birch_defn( $ns, 'render_errors', function() use ( $ns ) {
                $errors = $ns->get_errors();
                if ( $errors && sizeof( $errors ) > 0 ) {
                    echo '<div id="birchschedule_errors" class="error fade">';
                    foreach ( $errors as $error ) {
                        echo '<p>' . $error . '</p>';
                    }
                    echo '</div>';
                    update_option( 'birchschedule_errors', '' );
                }
            } );

        birch_defn( $ns, 'get_errors', function() {
                return get_option( 'birchschedule_errors' );
            } );

        birch_defn( $ns, 'has_errors', function() use ( $ns ) {
                $errors = $ns->get_errors();
                if ( $errors && sizeof( $errors ) > 0 ) {
                    return true;
                } else {
                    return false;
                }
            } );

        birch_defn( $ns, 'save_errors', function( $errors ) {
                update_option( 'birchschedule_errors', $errors );
            } );

        birch_defn( $ns, 'get_screen', function( $hook_name ) {
                global $birchpress;

                return $birchpress->view->get_screen( $hook_name );
            } );

        birch_defn( $ns, 'show_notice', function() {} );

        birch_defn( $ns, 'add_page_hook', function( $key, $hook ) use ( $ns, $_ns_data ) {
                $_ns_data->page_hooks[$key] = $hook;
            } );

        birch_defn( $ns, 'get_page_hook', function( $key ) use ( $ns, $_ns_data ) {
                if ( isset( $_ns_data->page_hooks[$key] ) ) {
                    return $_ns_data->page_hooks[$key];
                } else {
                    return '';
                }
            } );

        birch_defn( $ns, 'get_custom_code_css', function( $shortcode ) {
                return '';
            } );

        birch_defn( $ns, 'get_shortcodes', function() {
                return array();
            } );

        birch_defn( $ns, 'get_languages_dir', function() {
                return 'birchschedule/languages';
            } );

        birch_defn( $ns, 'load_i18n', function() use ( $ns ) {
                $lan_dir = $ns->get_languages_dir();
                load_plugin_textdomain( 'birchschedule', false, $lan_dir );
            } );

        birch_defn( $ns, 'create_admin_menus', function() use ( $ns ) {
                $ns->create_menu_scheduler();
                $ns->reorder_submenus();
            } );

        birch_defn( $ns, 'if_change_custom_menu_order', function() {
                return true;
            } );

        birch_defn( $ns, 'change_admin_menu_order', function( $menu_order ) {

                $custom_menu_order = array();

                $client_menu = array_search( 'edit.php?post_type=birs_client', $menu_order );

                foreach ( $menu_order as $index => $item ) {

                    if ( ( ( 'edit.php?post_type=birs_appointment' ) == $item ) ) {
                        $custom_menu_order[] = $item;
                        $custom_menu_order[] = 'edit.php?post_type=birs_client';
                        unset( $menu_order[$client_menu] );
                    } else {
                        if ( 'edit.php?post_type=birs_client' != $item )
                        $custom_menu_order[] = $item;
                    }
                }

                return $custom_menu_order;
            } );

        birch_defn( $ns, 'create_menu_scheduler', function() use ( $ns ) {
                $page_hook_calendar =
                add_submenu_page( 'edit.php?post_type=birs_appointment', __( 'Calendar', 'birchschedule' ),
                    __( 'Calendar', 'birchschedule' ), 'edit_birs_appointments', 'birchschedule_calendar',
                    array( $ns, 'render_calendar_page' ) );
                $ns->add_page_hook( 'calendar', $page_hook_calendar );

                $page_hook_settings =
                add_submenu_page( 'edit.php?post_type=birs_appointment',
                    __( 'BirchPress Scheduler Settings', 'birchschedule' ),
                    __( 'Settings', 'birchschedule' ), 'manage_birs_settings',
                    'birchschedule_settings', array( $ns, 'render_settings_page' ) );
                $ns->add_page_hook( 'settings', $page_hook_settings );

                $page_hook_help = add_submenu_page( 'edit.php?post_type=birs_appointment',
                    __( 'Help', 'birchschedule' ), __( 'Help', 'birchschedule' ),
                    'read', 'birchschedule_help', array( $ns, 'render_help_page' ) );
                $ns->add_page_hook( 'help', $page_hook_help );
            } );

        birch_defn( $ns, 'render_calendar_page', function() {} );

        birch_defn( $ns, 'render_settings_page', function() {} );

        birch_defn( $ns, 'render_help_page', function() {} );

        birch_defn( $ns, 'reorder_submenus', function() use ( $ns ) {
                global $submenu;

                $sub_items = &$submenu['edit.php?post_type=birs_appointment'];
                $location = $ns->get_submenu( $sub_items, 'location' );
                $staff = $ns->get_submenu( $sub_items, 'staff' );
                $service = $ns->get_submenu( $sub_items, 'service' );
                $settings = $ns->get_submenu( $sub_items, 'settings' );
                $help = $ns->get_submenu( $sub_items, 'help' );
                $calendar = $ns->get_submenu( $sub_items, 'calendar' );
                $new_appointment = $ns->get_submenu( $sub_items, 'post-new.php?post_type=birs_appointment' );

                $sub_items = array(
                    $calendar,
                    $new_appointment,
                    $location,
                    $staff,
                    $service,
                    $settings,
                    $help
                );
            } );

        birch_defn( $ns, 'get_submenu', function( $submenus, $name ) use ( $ns ) {
                foreach ( $submenus as $submenu ) {
                    $pos = strpos( $submenu[2], $name );
                    if ( $pos || $pos === 0 ) {
                        return $submenu;
                    }
                }
                return false;
            } );

        birch_defn( $ns, 'register_script_data_fn', function( $handle, $data_name, $fn ) {
                global $birchpress;

                $birchpress->view->register_script_data_fn( $handle, $data_name, $fn );
            } );

        birch_defn( $ns, 'get_admin_i18n_messages', function() {
                global $birchschedule;
                return $birchschedule->view->get_frontend_i18n_messages();
            } );

        birch_defn( $ns, 'get_frontend_i18n_messages', function() {
                return array(
                    'Loading...' => __( 'Loading...', 'birchschedule' ),
                    'Loading appointments...' => __( 'Loading appointments...', 'birchschedule' ),
                    'Saving...' => __( 'Saving...', 'birchschedule' ),
                    'Save' => __( 'Save', 'birchschedule' ),
                    'Please wait...' => __( 'Please wait...', 'birchschedule' ),
                    'Schedule' => __( 'Schedule', 'birchschedule' ),
                    'Are you sure you want to cancel this appointment?' => __( 'Are you sure you want to cancel this appointment?', 'birchschedule' ),
                    'Your appointment has been cancelled successfully.' => __( 'Your appointment has been cancelled successfully.', 'birchschedule' ),
                    "The appointment doesn't exist or has been cancelled." => __( "The appointment doesn't exist or has been cancelled.", 'birchschedule' ),
                    'Your appointment has been rescheduled successfully.' => __( 'Your appointment has been rescheduled successfully.', 'birchschedule' ),
                    'Your appointment can not be cancelled now according to our booking policies.' => __( 'Your appointment can not be cancelled now according to our booking policies.', 'birchschedule' ),
                    'Your appointment can not be rescheduled now according to our booking policies.' => __( 'Your appointment can not be rescheduled now according to our booking policies.', 'birchschedule' ),
                    'There are no available times.' => __( 'There are no available times.', 'birchschedule' ),
                    '(Deposit)' => __( '(Deposit)', 'birchschedule' ),
                    'Reschedule' => __( 'Reschedule', 'birchschedule' ),
                    'Change' => __( 'Change', 'birchschedule' ),
                    'No Preference' => __( 'No Preference', 'birchschedule' ),
                    'All Locations' => __( 'All Locations', 'birchschedule' ),
                    'All Providers' => __( 'All Providers', 'birchschedule' )
                );
            } );

        birch_defn( $ns, 'render_ajax_success_message', function( $success ) {
                global $birchpress;

                $birchpress->view->render_ajax_success_message( $success );
            } );

        birch_defn( $ns, 'render_ajax_error_messages', function( $errors ) {
                global $birchpress;

                $birchpress->view->render_ajax_error_messages( $errors );
            } );

        birch_defn( $ns, 'get_query_array', function( $query, $keys ) {
                global $birchpress;
                return $birchpress->view->get_query_array( $query, $keys );
            } );

        birch_defn( $ns, 'get_query_string', function( $query, $keys ) {
                global $birchpress;

                return $birchpress->view->get_query_string( $query, $keys );
            } );

        birch_defn( $ns, 'get_script_data_fn_model', function() {
                global $birchschedule, $birchpress;
                return array(
                    'admin_url' => admin_url(),
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'all_schedule' => $birchschedule->model->schedule->get_all_calculated_schedule(),
                    'all_daysoff' => $birchschedule->model->get_all_daysoff(),
                    'gmt_offset' => $birchpress->util->get_gmt_offset(),
                    'future_time' => $birchschedule->model->get_future_time(),
                    'cut_off_time' => $birchschedule->model->get_cut_off_time(),
                    'fully_booked_days' => $birchschedule->model->schedule->get_fully_booked_days()
                );
            } );

        birch_defn( $ns, 'get_script_data_fn_view', function() use ( $ns ) {
                global $birchpress, $birchschedule;

                return array(
                    'datepicker_i18n_options' => $birchpress->util->get_datepicker_i18n_params(),
                    'fc_i18n_options' => $birchpress->util->get_fullcalendar_i18n_params(),
                    'i18n_messages' => $ns->get_frontend_i18n_messages(),
                    'i18n_countries' => $birchpress->util->get_countries(),
                    'i18n_states' => $birchpress->util->get_states(),
                    'plugin_url' => $birchschedule->plugin_url()
                );
            } );

        birch_defn( $ns, 'get_script_data_fn_admincommon', function() use ( $ns ) {
                return array(
                    'i18n_messages' => $ns->get_admin_i18n_messages()
                );
            } );

        birch_defn( $ns, 'register_common_scripts_data_fns', function() use ( $ns ) {
                $ns->register_script_data_fn( 'birchschedule_model', 'birchschedule_model',
                    array( $ns, 'get_script_data_fn_model' ) );
                $ns->register_script_data_fn( 'birchschedule_view', 'birchschedule_view',
                    array( $ns, 'get_script_data_fn_view' ) );
                $ns->register_script_data_fn( 'birchschedule_view_admincommon', 'birchschedule_view_admincommon',
                    array( $ns, 'get_script_data_fn_admincommon' ) );
            } );

        birch_defn( $ns, 'register_3rd_scripts', function() {
                global $birchpress, $birchschedule;

                $version = $birchschedule->get_product_version();

                $birchpress->view->register_3rd_scripts();
                wp_register_script( 'moment',
                    $birchschedule->plugin_url() . '/lib/assets/js/moment/moment.min.js',
                    array(), '1.7.0' );

                wp_register_script( 'jgrowl',
                    $birchschedule->plugin_url() . '/lib/assets/js/jgrowl/jquery.jgrowl.js',
                    array( 'jquery' ), '1.4.0' );

                wp_register_script( 'jscolor',
                    $birchschedule->plugin_url() . '/lib/assets/js/jscolor/jscolor.js',
                    array(), '1.4.0' );

                wp_deregister_script( 'select2' );
                wp_register_script( 'select2',
                    $birchschedule->plugin_url() . '/lib/assets/js/select2/select2.min.js',
                    array( 'jquery' ), '3.4.2' );

                wp_register_script( 'fullcalendar_birchpress',
                    $birchschedule->plugin_url() . '/lib/assets/js/fullcalendar/fullcalendar_birchpress.js',
                    array( 'jquery-ui-draggable', 'jquery-ui-resizable',
                        'jquery-ui-dialog', 'jquery-ui-datepicker',
                        'jquery-ui-tabs', 'jquery-ui-autocomplete' ), '1.6.4' );

                wp_register_script( 'filedownload_birchpress',
                    $birchschedule->plugin_url() . '/lib/assets/js/filedownload/jquery.fileDownload.js',
                    array( 'jquery' ), '1.4.0' );
            } );

        birch_defn( $ns, 'register_3rd_styles', function() {
                global $birchschedule;

                $version = $birchschedule->get_product_version();

                wp_register_style( 'fullcalendar_birchpress',
                    $birchschedule->plugin_url() . '/lib/assets/js/fullcalendar/fullcalendar.css',
                    array(), '1.5.4' );

                wp_register_style( 'jquery-ui-bootstrap',
                    $birchschedule->plugin_url() . '/lib/assets/css/jquery-ui-bootstrap/jquery-ui-1.9.2.custom.css',
                    array(), '0.22' );
                wp_register_style( 'jquery-ui-no-theme',
                    $birchschedule->plugin_url() . '/lib/assets/css/jquery-ui-no-theme/jquery-ui-1.9.2.custom.css',
                    array(), '1.9.2' );
                wp_register_style( 'jquery-ui-smoothness',
                    $birchschedule->plugin_url() . '/lib/assets/css/jquery-ui-smoothness/jquery-ui-1.9.2.custom.css',
                    array(), '1.9.2' );

                wp_deregister_style( 'select2' );
                wp_register_style( 'select2',
                    $birchschedule->plugin_url() . '/lib/assets/js/select2/select2.css',
                    array(), '3.4.2' );

                wp_register_style( 'jgrowl',
                    $birchschedule->plugin_url() . '/lib/assets/js/jgrowl/jquery.jgrowl.css',
                    array(), '1.4.0' );
            } );

        birch_defn( $ns, 'register_common_scripts', function() {
                global $birchschedule;

                $version = $birchschedule->get_product_version();

                wp_register_script( 'birchschedule',
                    $birchschedule->plugin_url() . '/assets/js/base.js',
                    array( 'jquery', 'birchpress', 'birchpress_util' ), "$version" );

                wp_register_script( 'birchschedule_model',
                    $birchschedule->plugin_url() . '/assets/js/model/base.js',
                    array( 'jquery', 'birchpress', 'birchschedule' ), "$version" );

                wp_register_script( 'birchschedule_view',
                    $birchschedule->plugin_url() . '/assets/js/view/base.js',
                    array( 'jquery', 'birchpress', 'birchschedule', 'birchschedule_model' ), "$version" );

                wp_register_script( 'birchschedule_view_admincommon',
                    $birchschedule->plugin_url() . '/assets/js/view/admincommon/base.js',
                    array( 'jquery', 'birchpress', 'birchschedule', 'jgrowl' ), "$version" );

                wp_register_script( 'birchschedule_view_clients_edit',
                    $birchschedule->plugin_url() . '/assets/js/view/clients/edit/base.js',
                    array( 'birchschedule_view_admincommon', 'birchschedule_view' ), "$version" );

                wp_register_script( 'birchschedule_view_locations_edit',
                    $birchschedule->plugin_url() . '/assets/js/view/locations/edit/base.js',
                    array( 'birchschedule_view_admincommon', 'birchschedule_view' ), "$version" );

                wp_register_script( 'birchschedule_view_services_edit',
                    $birchschedule->plugin_url() . '/assets/js/view/services/edit/base.js',
                    array( 'birchschedule_view_admincommon', 'birchschedule_view' ), "$version" );

                wp_register_script( 'birchschedule_view_staff_edit',
                    $birchschedule->plugin_url() . '/assets/js/view/staff/edit/base.js',
                    array( 'birchschedule_view_admincommon', 'birchschedule_view',
                        'jscolor' ), "$version" );

                wp_register_script( 'birchschedule_view_calendar',
                    $birchschedule->plugin_url() . '/assets/js/view/calendar/base.js',
                    array( 'birchschedule_view_admincommon', 'birchschedule_view',
                        'fullcalendar_birchpress', 'moment' ), "$version" );

                wp_register_script( 'birchschedule_view_bookingform',
                    $birchschedule->plugin_url() . '/assets/js/view/bookingform/base.js',
                    array( 'jquery-ui-datepicker', 'birchschedule_view' ), "$version" );
            } );

        birch_defn( $ns, 'register_common_styles', function() {
                global $birchschedule;

                $version = $birchschedule->get_product_version();

                wp_register_style( 'birchschedule_admincommon',
                    $birchschedule->plugin_url() . '/assets/css/admincommon/base.css',
                    array( 'jgrowl', 'select2' ), "$version" );

                wp_register_style( 'birchschedule_calendar',
                    $birchschedule->plugin_url() . '/assets/css/calendar/base.css',
                    array( 'jgrowl' ), "$version" );

                wp_register_style( 'birchschedule_appointments_edit',
                    $birchschedule->plugin_url() . '/assets/css/appointments/edit/base.css',
                    array( 'jquery-ui-no-theme' ), "$version" );

                wp_register_style( 'birchschedule_appointments_new',
                    $birchschedule->plugin_url() . '/assets/css/appointments/new/base.css',
                    array( 'jquery-ui-no-theme' ), "$version" );

                wp_register_style( 'birchschedule_services_edit',
                    $birchschedule->plugin_url() . '/assets/css/services/edit/base.css',
                    array(), "$version" );

                wp_register_style( 'birchschedule_staff_edit',
                    $birchschedule->plugin_url() . '/assets/css/staff/edit/base.css',
                    array(), "$version" );

                wp_register_style( 'birchschedule_locations_edit',
                    $birchschedule->plugin_url() . '/assets/css/locations/edit/base.css',
                    array(), "$version" );

                wp_register_style( 'birchschedule_bookingform',
                    $birchschedule->plugin_url() . '/assets/css/bookingform/base.css',
                    array( 'jquery-ui-no-theme' ), "$version" );
            } );
    } );
