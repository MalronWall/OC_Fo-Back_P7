<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Representation;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class DefaultRepresentation
{
    /** @var Pagerfanta */
    public $datas;
    /** @var CollectionType */
    public $metas;

    /**
     * AbstractRepresentation constructor.
     * @param Pagerfanta $datas
     */
    public function __construct(Pagerfanta $datas)
    {
        $this->datas = $datas;

        // $limit
        $this->addMeta('limit_per_page', $datas->getMaxPerPage());
        // count results of the current page
        $this->addMeta('current_items', count($datas->getCurrentPageResults()));
        // nb total of results in all pages
        $this->addMeta('total_items', $datas->getNbResults());
        // offset / limit => nb total items passed + 1 / limit per page ALL rounded up
        $this->addMeta('current_page', ceil($datas->getCurrentPageOffsetStart()/$datas->getMaxPerPage()));
        // nb total of results / limit
        $this->addMeta('total_pages', ceil($datas->getNbResults()/$datas->getMaxPerPage()));
    }

    /**
     * @param $name
     * @param $value
     */
    public function addMeta($name, $value)
    {
        if (isset($this->metas[$name])) {
            throw new \LogicException(
                sprintf(
                    'This meta already exists. 
                    You are trying to override this meta, use the setMeta method instead for the %s meta.',
                    $name
                )
            );
        }

        $this->setMeta($name, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function setMeta($name, $value)
    {
        $this->metas[$name] = $value;
    }
}
