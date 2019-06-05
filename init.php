<?php
// Kleeja Plugin
// Developer: Kleeja team


// Prevent illegal run
if (! defined('IN_PLUGINS_SYSTEM'))
{
    exit();
}

// Plugin Basic Information
$kleeja_plugin['advanced_watermark']['information'] = [
    // The casual name of this plugin, anything can a human being understands
    'plugin_title' => [
        'en' => 'Advanced Watermark',
        'ar' => 'الختم على الصور المتطور'
    ],
    // Who wrote this plugin?
    'plugin_developer' => 'Kleeja.com',
    // This plugin version
    'plugin_version' => '1.0',
    // Explain what is this plugin, why should I use it?
    'plugin_description' => [
        'en' => 'Advanced watermark features',
        'ar' => 'خصائص الختم على الصور المتقدمة'
    ],
    //settings page, if there is one (what after ? like cp=j_plugins)
    'settings_page' => 'cp=watermark_settings',

    // Min version of Kleeja that's required to run this plugin
    'plugin_kleeja_version_min' => '3.1',
    // Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '3.9',
    // Should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0
];

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['advanced_watermark']['first_run']['ar'] = '
شكراً لاستخدامك هذه الإضافة
';
$kleeja_plugin['advanced_watermark']['first_run']['en'] = '
Thanks for using this plugin.
';

// Plugin Installation function
$kleeja_plugin['advanced_watermark']['install'] = function ($plg_id) {
    //new options
    $options = [
        'watermark_type' =>
        [
            'value'  => 'image',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_position' =>
        [
            'value'  => 'br',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_image_path' =>
        [
            'value'  => 'images/watermark.gif',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_text_content' =>
        [
            'value'  => 'kleeja.com',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_text_size' =>
        [
            'value'  => '15',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_text_color' =>
        [
            'value'  => '#fff',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_text_background' =>
        [
            'value'  => '#ccc',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_text_background_enable' =>
        [
            'value'  => '1',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
        'watermark_text_font' =>
        [
            'value'  => 'default',
            'plg_id' => $plg_id,
            'type'   => 'advanced_watermark'
        ],
    ];


    add_config_r($options);


    //new language variables
    add_olang([
        'R_WATERMARK_SETTINGS'                           => 'إعدادات الختم على الصور',
        'WATERMARK_POSITION'                             => 'مكان الختم على الصورة',
        'WATERMARK_POSITION_TL'                          => 'أعلى يسار',
        'WATERMARK_POSITION_TR'                          => 'أعلى يمين',
        'WATERMARK_POSITION_TC'                          => 'أعلى وسط',
        'WATERMARK_POSITION_BL'                          => 'أسفل يسار',
        'WATERMARK_POSITION_BR'                          => 'أسفل يمين',
        'WATERMARK_POSITION_BC'                          => 'أسفل وسط',
        'WATERMARK_TYPE'                                 => 'نوع الختم على الصورة',
        'WATERMARK_TYPE_IMAGE'                           => 'صورة مائية',
        'WATERMARK_TYPE_TEXT'                            => 'نص مكتوب',
        'WATERMARK_IMAGE_PATH'                           => 'مسار صورة الختم',
        'WATERMARK_TEXT_CONTENT'                         => 'نص الكتابة',
        'WATERMARK_TEXT_SIZE'                            => 'حجم حجم خط الكتابة',
        'WATERMARK_TEXT_COLOR'                           => 'لون خط الكتابة',
        'WATERMARK_TEXT_BACKGROUND'                      => 'لون خلفية الكتابة',
        'WATERMARK_TEXT_BACKGROUND_ENABLE'               => 'تفعيل وضع خلفية للنص',
        'WATERMARK_TEXT_FONT'                            => 'نوع خط الكتابة',
        'WATERMARK_NOT_ENABLED_NOTE'                     => 'يمكنك تفعيل ختم الصور من اعدادات كل مجموعة.',
        'WATERMARK_PREVIEW'                              => 'معاينة الختم',
    ],
        'ar',
        $plg_id);

    add_olang([
        'R_WATERMARK_SETTINGS'                           => 'Watermark Settings',
        'WATERMARK_POSITION'                             => 'Watermark position',
        'WATERMARK_POSITION_TL'                          => 'Top Left',
        'WATERMARK_POSITION_TR'                          => 'Top Right',
        'WATERMARK_POSITION_TC'                          => 'Top Center',
        'WATERMARK_POSITION_BL'                          => 'Bottom Left',
        'WATERMARK_POSITION_BR'                          => 'Bottom Right',
        'WATERMARK_POSITION_BC'                          => 'Bottom Center',
        'WATERMARK_TYPE'                                 => 'Watermark type',
        'WATERMARK_TYPE_IMAGE'                           => 'Image',
        'WATERMARK_TYPE_TEXT'                            => 'Text',
        'WATERMARK_IMAGE_PATH'                           => 'Watermark image path',
        'WATERMARK_TEXT_CONTENT'                         => 'Text content',
        'WATERMARK_TEXT_SIZE'                            => 'Text size',
        'WATERMARK_TEXT_COLOR'                           => 'Text color',
        'WATERMARK_TEXT_BACKGROUND'                      => 'Text background',
        'WATERMARK_TEXT_BACKGROUND_ENABLE'               => 'Enable watermark background',
        'WATERMARK_TEXT_FONT'                            => 'Text FontType',
        'WATERMARK_NOT_ENABLED_NOTE'                     => 'You can enable watermark for any group from users/any-group settings.',
        'WATERMARK_PREVIEW'                              => 'Watermark Preview',
    ],
        'en',
        $plg_id);
};


//Plugin update function, called if plugin is already installed but version is different than current
$kleeja_plugin['advanced_watermark']['update'] = function ($old_version, $new_version) {
    // if(version_compare($old_version, '0.5', '<')){
    // 	//... update to 0.5
    // }
    //
    // if(version_compare($old_version, '0.6', '<')){
    // 	//... update to 0.6
    // }

    //you could use update_config, update_olang
};


// Plugin Uninstallation, function to be called at unistalling
$kleeja_plugin['advanced_watermark']['uninstall'] = function ($plg_id) {
    //delete options
    delete_config([
        'watermark_type',
        'watermark_image_path',
        'watermark_text_content',
        'watermark_text_size',
        'watermark_text_color',
        'watermark_text_background',
        'watermark_text_background_enable',
        'watermark_text_font',
    ]);

    //delete language variables
    delete_olang(null, ['ar', 'en'], $plg_id);
};


// Plugin functions
$kleeja_plugin['advanced_watermark']['functions'] = [
    //add to admin menu
    'begin_admin_page' => function ($args) {
        $adm_extensions = $args['adm_extensions'];
        $ext_icons = $args['ext_icons'];

        $adm_extensions[] = 'watermark_settings';
        $ext_icons['watermark_settings'] = 'image';
        return compact('adm_extensions', 'ext_icons');
    },
    //add as admin page to reach when click on admin menu item we added.
    'not_exists_watermark_settings' => function() {
        $include_alternative = dirname(__FILE__) . '/watermark_settings.php';

        return compact('include_alternative');
    },

    'helper_watermark_func' => function ($args) {
        include_once __DIR__ . '/watermark.php';

        adv_helper_watermark($args['name'], strtolower($args['ext']));

        return ['return' => true];
    }

];
