<?php
/** Freesewing\Services\Service class */
namespace Freesewing\Services;

/**
 * Abstract service class for services
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Service
{

    /**
     * Returns the name of the service
     *
     * This is used to load the default theme for the service when no theme is specified
     *
     * @see Context::loadTheme()
     *
     * @return string
     */
    abstract public function getServiceName();

    /**
     * @param \Freesewing\Context $context
     *
     * @return void
     */
    abstract public function run(\Freesewing\Context $context);
}
