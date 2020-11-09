<?php

namespace App\Controller;

use App\DTO\Another\ActivateUserDTORequest;
use App\DTO\RegisterDTORequest;
use App\Entity\User;
use App\Http\ApiResponse;
use App\Security\InvitedUserAuthenticationSuccessHandler;
use App\Security\InvitedUserProvider;
use App\Service\Base64ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

    /**
     * Register main user
     *
     * @Route("/register", name="app_register", methods={"POST"})
     *
     * @param RegisterDTORequest $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param AuthenticationSuccessHandler $authHandler
     * @param Base64ImageService $imageService
     * @return \Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse
     */
    public function register(
        RegisterDTORequest $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        AuthenticationSuccessHandler $authHandler,
        Base64ImageService $imageService
    ) {
        $user = new User();
        $entity = $request->populateEntity($user);
        $encodedPassword = $encoder->encodePassword($entity, $request->password);
        $entity->setPassword($encodedPassword);
        $entity->setRoles([User::ROLE_USER]);

        if($request->img_encoded){
            $imgDirectory = User::UPLOAD_DIRECTORY . '/' . $user->getId() . '/' . User::AVATAR_PATH ;
            $imgPath = $imageService->saveImage($request->img_encoded, $imgDirectory, uniqid());
            $entity->setImg($imgPath);
        }

        $em->persist($entity);
        $em->flush($entity);
        $response = $authHandler->handleAuthenticationSuccess($entity);

        return $response;
    }


    /**
     * @Route("/logout", name="app_logout")
     *
     * @throws \Exception
     */
    public function logout()
    {

    }

}
