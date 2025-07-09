<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SiteAuthService;

class LeadListController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title'             => 'Lead',
            'controller'        => 'LeadListController',
            'controller_route'  => 'lead-list',
            'primary_key'       => 'id',
            // 'table_name'        => 'leads',
        );
    }

    public function list()
    {
        $data['module']                 = $this->data;
        $title                          = $this->data['title'].' List';
        $page_name                      = 'lead.list';
        $data                           = $this->siteAuthService ->admin_after_login_layout($title,$page_name,$data);
        return view('maincontents.' . $page_name, $data);
    }
}
