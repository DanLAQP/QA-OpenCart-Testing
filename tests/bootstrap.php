<?php
/**
 * Bootstrap file for PHPUnit
 *
 * Configures the test environment y carga dependencias necesarias
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define database constants (mock values for testing)
if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', 'oc_');
}

// Autoloader
require_once(__DIR__ . '/../vendor/autoload.php');

// Test base class loader
require_once(__DIR__ . '/BaseTestCase.php');
