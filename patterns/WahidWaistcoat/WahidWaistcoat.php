<?php
/** Freesewing\Patterns\WahidWaistcoat class */
namespace Freesewing\Patterns;

/**
 * The Wahid Waistcoat pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class WahidWaistcoat extends JoostBodyBlock
{
    /**
     * Add parts in config file to pattern
     *
     * I override the pattern's class loadParts() here
     * because I want to not render the blocks that we're
     * getting from the parent.
     */
    public function loadParts()
    {
        foreach ($this->config['parts'] as $part => $title) {
            $this->addPart($part);
            $this->parts[$part]->setTitle($title);
        }
        //$this->parts['frontBlock']->setRender(false);
        //$this->parts['backBlock']->setRender(false);
    }
    
    /**
     * Generates a draft of the pattern
     *
     * This creates a draft of this pattern for a given model
     * and set of options. You get a complete pattern with 
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->buildCore($model);

    }

    /**
     * Generates a sample of the pattern
     *
     * This creates a sample of this pattern for a given model
     * and set of options. You get a barebones pattern with only 
     * what it takes to illustrate the effect of changes in
     * the sampled option or measurement.
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        $this->buildCore($model);

    }

    /**
     * Drafts the blocks this is based on
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function buildCore($model)
    {
        $this->validateOptions();
        $this->loadHelp($model);
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
    }

    /**
     * Makes sure options make sense
     *
     * @return void
     */
    public function validateOptions()
    {
        // shifTowards can't deal with 0, so shoulderStrapPlacement should be at least 0.001
        if ($this->getOption('shoulderStrapPlacement') == 0) {
            $this->setOption('shoulderStrapPlacement', 0.001);
        }
        // a stretchFactor below 50% is obviously wrong
        if ($this->getOption('stretchFactor') < 0.5) {
            $this->setOption('stretchFactor', 0.5);
        }
        // These are irrelevant, but needed for JoostBodyBlock
        $this->setOption('collarEase', 15);
        $this->setOption('backNeckCutout', 20);
    }

    /**
     * Drafts the front
     *
     * I'm using a draft[part name] scheme here but
     * don't let that think that this is something specific
     * to the draft service.
     * 
     * This draft method does the basic drafting and is
     * called by both the draft AND sample methods.
     *
     * The difference starts after this method is done.
     * For sample, this is all we need, but draft calls
     * the finalize[part name] method after this.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFront($model)
    {
        $this->clonePoints('frontBlock', 'front');
        $p = $this->parts['front'];

    }
}
