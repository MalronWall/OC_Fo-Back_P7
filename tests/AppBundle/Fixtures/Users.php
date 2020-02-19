<?php
/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace Tests\AppBundle\Fixtures;


use AppBundle\Domain\Entity\Client;
use AppBundle\Domain\Entity\User;

trait Users
{
    /** @var Client */
    private $client;
    /** @var User */
    private $johnDoe;
    /** @var User */
    private $janeDoe;

    public function buildUsers()
    {
        $this->client = new Client("SFR", "password");
        $this->johnDoe = new User("John", "Doe", "address", "cp", "city", "phoneNumber", $this->client);
        $this->janeDoe = new User("Jane", "Doe", "address", "cp", "city", "phoneNumber", $this->client);
    }
}