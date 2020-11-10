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
use GeoIp2\Exception\AddressNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class LinkTrackingController
 * @package App\Controller
 */
class LinkTrackingController extends AbstractController
{
    /**
     * Saves statistic for a link.
     * We use Max Ming geo ip database for country detecting and fill "country" table with names of countries,
     * so in statistic calculation we may use country id for calculation
     *
     * @Route("track/{guid}", name="links_list", requirements={"guid":".*"},  methods={"GET"})
     *
     * @param string $guid
     * @param EntityManagerInterface $entityManager
     * @param LinkRepository $linkRepository
     * @param PlatformRepository $platformRepository
     * @param CountryRepository $countryRepository
     * @param Reader $geoIpReader
     * @param LoggerInterface $logger
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
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
            $entityManager->flush();
            throw new HttpException(Response::HTTP_GONE, 'Link is expired!');
        }
        $newVisit = new Visit();
        $newVisit->setLink($link);
        $newVisit->setIp($_SERVER['REMOTE_ADDR']);
        $newVisit->setReferer($_SERVER['HTTP_REFERER'] ?? null);
        $newVisit->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $browserInfo = get_browser(null, true); //requires browscap to be confidured

        //platform might be empty
        if($browserInfo['platform']){
            $platformRepository->createPlatformIfDoesntExist($browserInfo['platform'], false);
        }
        //localhost  ip can not be found in db, so it causes an exception
        // .... .. .0.1 appears when the request is originated from the same host as the container. In real case it should work fine
        try{
            $countryName = $geoIpReader->country($_SERVER['REMOTE_ADDR']);
            $country = $countryRepository->createCountryIfDoesntExist($countryName->country->name, false);
            $newVisit->setCountry($country);
        } catch (AddressNotFoundException $e){
            $logger->info('Can\'t detect country for an ip ' . $_SERVER['REMOTE_ADDR'] . ' ' . $e->getMessage());
        }

        $entityManager->persist($newVisit);
        $entityManager->flush();

        return new ApiResponse($newVisit);
    }

}
