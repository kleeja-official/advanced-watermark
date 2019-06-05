<?php

// not for directly open
if (! defined('IN_ADMIN'))
{
    exit;
}


//current case
$current_case = g('case', 'str', 'view');

//current template
$stylee = 'admin_watermark_settings';

// template variables
$styleePath = dirname(__FILE__);
$action     = basename(ADMIN_PATH) . '?cp=' . basename(__file__, '.php');

$H_FORM_KEYS	     = kleeja_add_form_key('adm_watermark_settings');

if(ip('preview'))
{
    $case      = 'preview';
}

switch ($current_case)
{
    /**
     * show a list of current ftp accounts
     */
    default:
    case 'view':

        $generated_preview_path = false;

    break;
    /**
     * upload watermark
     */
    case 'upload':

        if (intval($userinfo['founder']) !== 1)
        {
            kleeja_admin_info($lang['HV_NOT_PRVLG_ACCESS'], $action);
        }

        if (! kleeja_check_form_key('adm_watermark_settings', 1000))
        {
            kleeja_admin_err($lang['INVALID_FORM_KEY'], $action);
        }

        if (empty($_FILES['newimage']['tmp_name']))
        {
            kleeja_admin_err($lang['CHOSE_F'], $action);
        }

        $ext = strtolower(array_pop(explode('.', $_FILES['newimage']['name'])));

        if (! in_array($ext, ['png', 'gif', 'jpg']))
        {
            kleeja_admin_err(sprintf($lang['FORBID_EXT'], $ext), $action);
        }

        $image_path  = $config['foldername'] . '/watermark' . mt_rand() . '.' . $ext;
        $upload_path = PATH . ltrim($image_path, '/');

        $file = move_uploaded_file($_FILES['newimage']['tmp_name'], $upload_path);

        if ($file)
        {
            update_config('watermark_image_path', $image_path);
            kleeja_admin_info(sprintf($lang['ITEM_UPDATED'], $olang['WATERMARK_IMAGE_PATH']), $action);
        }
        else
        {
            kleeja_admin_err($lang['ERROR_TRY_AGAIN'], $action);
        }

        break;



    case 'update':
    case 'preview':

        if (! kleeja_check_form_key('adm_watermark_settings', 1000))
        {
            kleeja_admin_err($lang['INVALID_FORM_KEY'], $action);
        }


        $list  = [
            'watermark_type',
            'watermark_position',
            'watermark_text_content',
            'watermark_text_size',
            'watermark_text_color',
            'watermark_text_background',
            'watermark_text_font',
            'watermark_text_background_enable',
        ];


        $_POST['watermark_text_background_enable'] = ip('watermark_text_background_enable') ? 1 : 0;

        foreach ($list as $item)
        {
            update_config($item, p($item, 'str'));
        }

        delete_cache('data_config');


        if($case != 'preview')
        {
            kleeja_admin_info($lang['CONFIGS_UPDATED'], $action);
            break;
        }

        include_once __DIR__ . '/watermark.php';

        $generated_preview_path = PATH . ltrim($config['foldername']) . '/preview_watermark.jpg';
        copy(__DIR__ . '/preview.jpg', $generated_preview_path);
        adv_helper_watermark($generated_preview_path, 'jpg');


        $case  = 'view';
        break;
}
