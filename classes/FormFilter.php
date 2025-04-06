<?php
class FormFilter {
    public static function getFilter() {
        return isset($_GET['filter']) ? $_GET['filter'] : '';
    }
}