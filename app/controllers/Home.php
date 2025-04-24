<?php
class Home extends Controller {
    public function __construct() {
        // Any constructor logic if needed
    }

    public function index() {
        $data = [
            'title' => 'Welcome to Hopefull',
            'description' => 'Connect with causes that matter and make a real impact in people\'s lives'
        ];

        $this->view('home/index', $data);
    }
}