<?php
    function getIuser() {
        return isset($_SESSION[_LOGINUSER]) ? $_SESSION[_LOGINUSER] : 0;
    }