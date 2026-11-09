<?php
    $connection = mysqli_connect("localhost","root","","dbmining");

    if (!function_exists('isYes')) {
        function isYes($val) {
            $v = strtolower(trim($val));
            return in_array($v, ['yes', 'ya', 'layak', 'layak (yes)', 'layak (ya)', '1']);
        }
    }

    if (!function_exists('isNo')) {
        function isNo($val) {
            $v = strtolower(trim($val));
            return in_array($v, ['no', 'tidak', 'tidak layak', 'tidak layak (no)', 'tidak layak (tidak)', '0']);
        }
    }
?>
