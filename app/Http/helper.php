<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('adminUrl')) {
    function adminUrl($url = null)
    {
        return url('admin/' . $url);
    }
}

if (!function_exists('admin')) {
    function admin()
    {
        return auth()->guard('admin');
    }
}

if (!function_exists('active_menu')) {
    function active_menu($link)
    {
        if (preg_match('/'.$link.'/i',Request::segment(2))){
            return ['menu-open','display:block'];
        }else{
            return ['',''];
        }
    }
}

if (!function_exists('lang')) {
    function lang()
    {
        if (session()->has('lang')) {
            return session('lang');
        } else {
            return 'en';
        }
    }
}

if (!function_exists('appDirection')) {
    function appDirection()
    {
        if (session()->has('lang')) {
            if (session('lang') == 'ar'){
                return 'rtl';
            }else{
                return 'ltr';
            }
        } else {
            return 'ltr';
        }
    }
}

if (!function_exists('datatablesLang')) {
    function datatablesLang()
    {
        return [
            'sProcessing' => trans('admin.sProcessing'),
            'sLengthMenu' => trans('admin.sLengthMenu'),
            'sZeroRecords' => trans('admin.sZeroRecords'),
            'sEmptyTable' => trans('admin.sEmptyTable'),
            'sInfo' => trans('admin.sInfo'),
            'sInfoEmpty' => trans('admin.sInfoEmpty'),
            'sInfoFiltered' => trans('admin.sInfoFiltered'),
            'sInfoPostFix' => trans('admin.sInfoPostFix'),
            'sSearch' => trans('admin.sSearch'),
            'sUrl' => trans('admin.sUrl'),
            'sInfoThousands' => trans('admin.sInfoThousands'),
            'sLoadingRecords' => trans('admin.sLoadingRecords'),
            'oPaginate' => [
                'sFirst' => trans('admin.sFirst'),
                'sLast' => trans('admin.sLast'),
                'sNext' => trans('admin.sNext'),
                'sPrevious' => trans('admin.sPrevious'),
            ],
            'oAria' => [
                'sSortAscending' => trans('admin.sSortAscending'),
                'sSortDescending' => trans('admin.sSortDescending'),
            ],
        ];
    }
}
