<?php
    namespace application\controllers;

    class UserController extends Controller {
        public function signUp() {
            $json = getJson();
            $result = $this->model->signUp($json);
            if($result) {
                $this->flash(_LOGINUSER, $result);
                return [_RESULT => 1];
            }
            return [_RESULT => 0];
        }
    }