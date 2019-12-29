<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Representation;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Serializer\Annotation\Groups;

class DefaultRepresentation
{
    /**
     * @var Pagerfanta
     *
     * @Groups({"user_list", "phone_list", "client_list"})
     */
    public $datas;
    /**
     * @var CollectionType
     *
     * @Groups({"user_list", "phone_list", "client_list"})
     */
    public $metas;

    /**
     * @param Pagerfanta $datas
     * @return $this
     */
    public function defaultDisplay(Pagerfanta $datas)
    {
        $this->datas = $datas;

        // $limit
        $this->addMeta('limitPerPage', $datas->getMaxPerPage());
        // count results of the current page
        $this->addMeta('currentItems', count($datas->getCurrentPageResults()));
        // nb total of results in all pages
        $this->addMeta('totalItems', $datas->getNbResults());
        // offset / limit => nb total items passed + 1 / limit per page ALL rounded up
        $this->addMeta('currentPage', ceil($datas->getCurrentPageOffsetStart()/$datas->getMaxPerPage()));
        // nb total of results / limit
        $this->addMeta('totalPages', ceil($datas->getNbResults()/$datas->getMaxPerPage()));

        return $this;
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
