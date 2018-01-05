<?php

/**
 * @file
 * The PHP page that serves all Freesewing requests
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 * @see https://github.com/joostdecock/freesewing Code repository on GitHub
 */

// Sensible error settings
ini_set("display_errors", 0);
ini_set("log_errors", 1);

require __DIR__.'/vendor/autoload.php';

$context = new \Freesewing\Context();
$context->setRequest(new \Freesewing\Request($_REQUEST));
$context->configure();

// Rollbar integration
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;
if($context->getConfig()['integrations']['rollbar'] !== false) {
    Rollbar::init([
        'access_token' => getenv('ROLLBAR_ACCESS_TOKEN'),
        'environment' => getenv('ROLLBAR_ENVIRONMENT'),
        'included_errno' => (E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR | E_NOTICE)
    ]);
}

$context->runService();
