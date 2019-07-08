<?php
Class Auth_c extends Controller
{
    public function __construct()
    {
        // URLセグメントから正規表現引数を取得 (省略可能)
        $reg = load::l('router')->reg_segments;
    }
    
    public function index()
    {
        
    }
}
