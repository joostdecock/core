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

require __DIR__.'/vendor/autoload.php';

$context = new \Freesewing\Context();
$context->setRequest(new \Freesewing\Request($_REQUEST));
$context->configure();

$context->runService();
