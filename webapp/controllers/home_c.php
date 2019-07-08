<?php
Class Home_c extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // URLセグメントから正規表現引数を取得 (省略可能)
        $reg = load::l('router')->reg_segments;

        $this->setOutput(array('hello'=>'Hello World!'));
    }
}
