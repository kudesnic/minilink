<?php

namespace App\Controller;

use App\DTO\LinkInsertDTORequest;
use App\DTO\LinkUpdateDTORequest;
use App\Entity\Link;
use App\Http\ApiResponse;
use App\Security\LinkVoter;
use App\Service\JWTUserService;
use App\Service\PaginationServiceByQueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class LinkController
 * @package App\Controller
 * @Route("/link", name="link.")
 */
class LinkController extends AbstractController
{
    /**
     * Gets users from the same tree and all levels
     *
     * @Route("", name="list",  defaults={"page": 1},  methods={"GET"})
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
        $qb = $repository->userLinksQueryBulder($user);
        if($request->query->has('status')){
            $status = json_encode([$request->query->get('status')], true);
            $qb->andWhere('status = ' . $status);
        }
        $result = $paginationManger->setRepository(Link::class)
                ->paginate($qb, $page, null);

        return new ApiResponse($result);
    }


    /**
     * @Route("/{id}", name="show", requirements={"id":"\d+"},  methods={"GET"})
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
     * @Route("", name="store", methods={"POST"})
     *
     * @param LinkInsertDTORequest $request
     * @param JWTUserService $userHolder
     * @param EntityManagerInterface $entityManager
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function store(
        LinkInsertDTORequest $request,
        JWTUserService $userHolder,
        EntityManagerInterface $entityManager
    ) {
        $user = $userHolder->getUser($request->getRequest());
        $linkEntity = new Link();
        $linkEntity->setOwner($user);
        $linkEntity->setLink($request->link);
        $linkEntity->setExpirationTime(time() + $request->expiration_time);
        $linkEntity->setStatus($request->status ?? Link::STATUS_ACTIVE);
        $entityManager->persist($linkEntity);
        $entityManager->flush();

        return new ApiResponse($linkEntity, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="update", requirements={"id":"\d+"}, methods={"PUT"})
     *
     * @param Link $link
     * @param LinkUpdateDTORequest $request
     * @param EntityManagerInterface $entityManager
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function update(
        Link $link,
        LinkUpdateDTORequest $request,
        EntityManagerInterface $entityManager
    ) {
        $this->denyAccessUnlessGranted(LinkVoter::EDIT, $link);
        $linkEntity = $request->populateEntity($link);
        $entityManager->persist($linkEntity);
        $entityManager->flush();

        return new ApiResponse($linkEntity, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="delete", requirements={"id":"\d+"},  methods={"DELETE"})
     *
     * @param Link $link
     * @param EntityManagerInterface $entityManager
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function destroy(Link $link, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted(LinkVoter::DELETE, $link);
        $entityManager->remove($link);
        $entityManager->flush();

        return new ApiResponse();
    }

}
