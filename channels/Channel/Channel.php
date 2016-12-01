<?php
/** Freesewing\Channels\Channel */
namespace Freesewing\Channels;

/**
 * Abstract class for channels.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Channel
{
    /** @var array $options Array of theme options */
    public $options = array();

    /** @var array $config The channel configuration */
    private $config = array();

    /**
     * Constructor loads the Yaml config into the config property
     *
     * @throws InvalidArgument if the Yaml file is not valid
     */
    public function __construct()
    {
        $this->config = \Freesewing\Yamlr::loadYamlFile(\Freesewing\Utils::getClassDir($this).'/config.yml');
    }

    /**
     * Allows the channel designer to implement access control
     *
     * You may not want to make your channel publically accessible.
     * You can limit access here in whatever way you like.
     * You have access to the entire context to decide what to do.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return bool true Always true in this case
     */
    public function isValidRequest($context)
    {
        return true;
    }

    /**
     * What to do when a request is considered to be invalid
     *
     * If you return false isValidRequest() then we need to do 
     * something with the ongoing request. Since you decided it's 
     * no good, you get to decide what to do with it.
     *
     * By default, we redirect to the documentation.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return void Redirect to the documentation
     */ 
    public function handleInValidRequest($context)
    {
        header("location: /api/docs/");
    }

    /**
     * Clean up does nothing
     *
     * By default, there's nothing to clean up. But if your channel
     * is logging to a database (for example), you could close that
     * database connection here.
     *
     * @return void Nothing
     */
    public function cleanUp()
    {
    }

    /**
     * Turn input into model measurements that we understand.
     *
     * Each channel must implement this function.
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\[pattern] $pattern The pattern object
     *
     * @return array The model measurements
     */ 
    abstract public function standardizeModelMeasurements($request, $pattern);

    /**
     * Turn input into pattern options that we understand.
     *
     * Each channel must implement this function.
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\[pattern] $pattern The pattern object
     *
     * @return array The pattern options
     */ 
    abstract public function standardizePatternOptions($request, $pattern);
}
