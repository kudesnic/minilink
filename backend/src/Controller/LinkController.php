<?php

namespace App\Controller;

use App\DTO\LinkInsertDTORequest;
use App\Entity\Link;
use App\Entity\User;
use App\Http\ApiResponse;
use App\Repository\LinkRepository;
use App\Security\LinkVoter;
use App\Security\UserVoter;
use App\Service\JWTUserService;
use App\Service\PaginationServiceByQueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class LinkController
 * @package App\Controller
 */
class LinkController extends AbstractController
{
    /**
     * Gets users from the same tree and all levels
     *
     * @Route("/link", name="links_list",  defaults={"page": 1},  methods={"GET"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param PaginationServiceByQueryBuilder $paginationManger
     * @param JWTUserService $userHolder
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginationServiceByQueryBuilder $paginationManger,
        JWTUserService $userHolder
    ) {
        $page = $request->query->get('page');
        $user = $userHolder->getUser($request);
        $repository = $em->getRepository(Link::class);
        $qb = $repository->findAll($user);
        if($request->query->has('status')){
            $status = json_encode([$request->query->get('status')], true);
            $qb->andWhere('status = ' . $status);
        }
        $result = $paginationManger->setRepository(Link::class)
                ->paginate($qb, $page, null);

        return new ApiResponse($result);
    }


    /**
     * @Route("/link/{id}", name="link_show", requirements={"id":"\d+"},  methods={"GET"})
     *
     * @param Link $link
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show(Link $link)
    {
        $this->denyAccessUnlessGranted(LinkVoter::VIEW, $link);

        return new ApiResponse($link);
    }

    /**
     * @Route("/link", name="store", methods={"POST"})
     *
     * @param LinkInsertDTORequest $request
     * @param JWTUserService $userHolder
     * @param EntityManager $entityManager
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function store(
        LinkInsertDTORequest $request,
        JWTUserService $userHolder,
        EntityManager $entityManager
    ) {
        $user = $userHolder->getUser($request->getRequest());
        $linkEntity = new Link();
        $linkEntity->setUser($user);
        $linkEntity->setLink($request->link);
        $linkEntity->setLivingTime($request->living_time);
        $linkEntity->setStatus($request->status ?? Link::STATUS_ACTIVE);
        $entityManager->persist($linkEntity);
        $entityManager->flush();

        return new ApiResponse($linkEntity, Response::HTTP_CREATED);
    }

    /**
     * @Route("/link", name="store", methods={"POST"})
     *
     * @param LinkInsertDTORequest $request
     * @param JWTUserService $userHolder
     * @param EntityManager $entityManager
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function update(
        LinkInsertDTORequest $request,
        JWTUserService $userHolder,
        EntityManager $entityManager
    ) {
        $user = $userHolder->getUser($request->getRequest());
        $linkEntity = new Link();
        $linkEntity->setUser($user);
        $linkEntity->setLink($request->link);
        $linkEntity->setLivingTime($request->living_time);
        $linkEntity->setStatus($request->status ?? Link::STATUS_ACTIVE);
        $entityManager->persist($linkEntity);
        $entityManager->flush();

        return new ApiResponse($linkEntity, Response::HTTP_CREATED);
    }

}
