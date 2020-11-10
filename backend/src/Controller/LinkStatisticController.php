<?php

namespace App\Controller;

use App\Http\ApiResponse;
use App\Repository\LinkRepository;
use App\Repository\VisitRepository;
use App\Security\LinkVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LinkStatisticController
 * @package App\Controller
 */
class LinkStatisticController extends AbstractController
{
    /**
     * Collects statistic for a given link
     *
     * @Route("statistic/{guid}", name="base_statistic", requirements={"guid":".*"},  methods={"GET"})
     *
     * @param string $guid
     * @param VisitRepository $visitkRepository
     * @param LinkRepository $linkRepository
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getStatistic(string $guid, VisitRepository $visitkRepository, LinkRepository $linkRepository)
    {
        $link = $linkRepository->findOneBy(['guid' => $guid]);
        if(!$link){
            throw new NotFoundHttpException('Link not found');
        }
        $this->denyAccessUnlessGranted(LinkVoter::VIEW, $link);
        $result = [
            'per_country' => $visitkRepository->getCountryLinkStatistic($link),
            'per_platform' => $visitkRepository->getPlatformLinkStatistic($link),
            'total' => $visitkRepository->getTotalVisits($link)
        ];

        return new ApiResponse($result);
    }

}
