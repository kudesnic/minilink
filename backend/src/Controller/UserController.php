<?php

namespace App\Controller;

use App\Entity\User;
use App\Http\ApiResponse;
use App\Security\UserVoter;
use App\Service\JWTUserService;
use App\Service\PaginationServiceByQueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * Gets users from the same tree and all levels
     *
     * @Route("/user", name="users_list",  defaults={"page": 1},  methods={"GET"})
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
        $this->denyAccessUnlessGranted(UserVoter::VIEW_LIST);
        $repository = $em->getRepository(User::class);
        $qb = $repository->findAll($user);
        if($request->query->has('role')){
            $role = json_encode([$request->query->get('role')], true);
            $qb->andWhere('CONTAINS( node.roles, \'' . $role . '\') = true');
        }
        $result = $paginationManger->setRepository(User::class)
                ->paginate($qb, $page, null);

        return new ApiResponse($result);
    }


    /**
     * @Route("/user/{id}", name="user_show", requirements={"id":"\d+"},  methods={"GET"})
     *
     * @param User $user
     * @return ApiResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show(User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);

        return new ApiResponse($user);
    }

}
