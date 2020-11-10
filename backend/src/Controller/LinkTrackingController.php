<?php

namespace App\Controller;

use App\Entity\Link;
use App\Entity\Visit;
use App\Http\ApiResponse;
use App\Repository\CountryRepository;
use App\Repository\LinkRepository;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use GeoIp2\Database\Reader;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class LinkTrackingController
 * @package App\Controller
 */
class LinkTrackingController extends AbstractController
{
    /**
     * Saves statistic for a link
     *
     * @Route("track/{guid}", name="links_list", requirements={"guid":".*"},  methods={"GET"})
     *
     * @param string $guid
     * @param EntityManagerInterface $entityManager
     * @param LinkRepository $linkRepository
     * @param PlatformRepository $platformRepository
     * @param CountryRepository $countryRepository
     * @param Reader $geoIpReader
     * @param Logger $logger
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \HttpException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function track(
        string $guid,
        EntityManagerInterface $entityManager,
        LinkRepository $linkRepository,
        PlatformRepository $platformRepository,
        CountryRepository $countryRepository,
        Reader $geoIpReader,
        LoggerInterface $logger
    ) {
        $link = $linkRepository->findOneBy(['guid' => $guid, 'status' => Link::STATUS_ACTIVE]);
        if(!$link){
            throw new NotFoundHttpException('Link not found');
        }
        if($link->getExpirationTime() < time()){
            $link->setStatus(Link::STATUS_EXPIRED);
            $entityManager->persist($link);
            $entityManager->flash();
            throw new \HttpException('Link is expired!', Response::HTTP_GONE);
        }
        $newVisit = new Visit();
        $newVisit->setLink($link);
        $newVisit->setIp($_SERVER['REMOTE_ADDR']);
        $newVisit->setReferer($_SERVER['HTTP_REFERER'] ?? null);
        $newVisit->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $browserInfo = get_browser(null, true);

        //platform might be empty
        if($browserInfo['platform']){
            $platformRepository->createPlatformIfDoesntExist($browserInfo['platform'], false);
        }

//        try{
            $countryName = $geoIpReader->country($_SERVER['REMOTE_ADDR']);
            $country = $countryRepository->createCountryIfDoesntExist($countryName, false);
            $newVisit->setCountry($country);
//        } catch (\Error $e){
//            $logger->info('Can\'t detect country for an ip ' . $_SERVER['REMOTE_ADDR'] . ' ' . $e->getMessage());
//        }

        $entityManager->persist($newVisit);
        $entityManager->flash();

        return new ApiResponse($newVisit);
    }

}
