<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Common;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HateoasManager
{
    public const LIST = "list";
    public const SHOW = "show";
    public const CREATE = "create";
    public const DELETE = "delete";

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * HateoasManager constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param array $results
     * @param string $objectName
     * @param array $options
     * @return array
     */
    public function buildHateoas(array $results, string $objectName, array $options): array
    {
        $datas = [];
        foreach ($results["datas"] as $result) {
            $data = [];
            $data[$objectName] = $result;
            $data["links"] = $this->buildLink(
                $objectName,
                $options,
                $result->getId()->toString()
            );
            $datas[] = $data;
        }
        $results["datas"] = $datas;
        return $results;
    }

    private function buildLink(string $objectName, array $options, string $id) : array
    {
        $links = [];
        foreach ($options as $option) {
            $link = [];
            switch ($option) {
                case self::LIST:
                    $link["url"] = $this->urlGenerator->generate($objectName."_".$option);
                    $link["method"] = "GET";
                    break;
                case self::SHOW:
                    $link["url"] = $this->urlGenerator->generate($objectName."_".$option, ["id" => $id]);
                    $link["method"] = "GET";
                    break;
                case self::CREATE:
                    $link["url"] = $this->urlGenerator->generate($objectName."_".$option);
                    $link["method"] = "POST";
                    break;
                case self::DELETE:
                    $link["url"] = $this->urlGenerator->generate($objectName."_".$option, ["id" => $id]);
                    $link["method"] = "DELETE";
                    break;
            }
            $link["returnType"] = $option;
            $links[] = $link;
        }
        return $links;
    }
}
