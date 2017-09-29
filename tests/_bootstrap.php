<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

define('APPLICATION_STORE', 'DE');

$bootstrap = Spryker\Shared\Library\SystemUnderTest\SystemUnderTestBootstrap::getInstance();

$path = realpath(__DIR__ . '/../../../../../..');
define('APPLICATION_ROOT_DIR', $path);

$bootstrap->bootstrap('Zed');
