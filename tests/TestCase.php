<?php

namespace Tests;

use CI_Controller;
use WS_Controller;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Parent test case sharing common test functionality.
 */
class TestCase extends PHPUnitTestCase {
    /**
     * @var WS_Controller|CI_Controller
     */
    private static WS_Controller|CI_Controller $CI;

    /**
     * Load the framework instance.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$CI =& get_instance();
    }
}
