<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * User controller.
 *
 * @Route("/api")
 */
class UserController extends AbstractController
{

    /**
     * @Route(
     *     name="get_user",
     *     path="/user/get-user",
     *     methods={"GET"},
     *     defaults={
     *     
     *     }
     * )
     */
    public function getUserFromToken() {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $roleArr = [];
        
        if( $user ) {
            $qRoles =  $this->getDoctrine()->getRepository(Role::class)->getAccessListByRoles($user->getRoles());
            
            $roles = $qRoles;
            $roleArr = [];

            foreach($roles as $role)
            {
                if(\is_array( $role['accessList']))
                {
                    $roleArr = array_merge($roleArr, $role['accessList']);
                }
                
            }
        }

        return new JsonResponse([
            'id'        => $user->getId(),
            'email'     => $user->getEmail(),
            'username'  => $user->getUserName(),
            'roles'     => $user->getRoles(),
            'access'    => $roleArr
        ]);
    }

     /**
     * @Route(
     *     name="check_has_users",
     *     path="/user/check-has-users",
     *     methods={"GET"},
     *     defaults={
     *     
     *     }
     * )
     */
    public function checkHasUsers() {
        $em = $this->getDoctrine()->getManager();

        // check if there are users
        $users = $em->getRepository(User::class)->getcheckIfUsers();

        
        return new JsonResponse([
            'message' => $users
        ]);
    }

     /**
     * @Route(
     *     name="register_admin_user",
     *     path="/user/register-admin-user",
     *     methods={"POST"},
     *     defaults={
     *     
     *     }
     * )
     */
    public function registerAdminUser(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $em = $this->getDoctrine()->getManager();

        // check if there are users
        $users = $em->getRepository(User::class)->findAll();

        if( count($users) ) {
            return new JsonResponse([
                'message' => 'Admin user already created'
            ], Response::HTTP_FORBIDDEN);
        }
        
        $data = json_decode($request->getContent(), true);

        $email = $data['username'];
        $password = $data['password'];
        
        $user = new User();
        $user->setEmail($email);
        $password = $passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);
        $user->setRoles([
            "ROLE_USER", 
            "ROLE_ADMIN"
        ]);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'message' => 'Admin User successfully created!'
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function getUsers(Request $request): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        $serializer = new Serializer([$normalizer], [$encoder]);


        return $this->json( json_decode($serializer->serialize($users, 'json')));

        return $this->json($users);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/users", name="users_add", methods={"POST"})
     */
    public function addUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $request = $this->transformJsonBody($request);


            $user = new User();
            $user->setEmail($request->get('email'));
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $request->get('plainPassword'))
            );
            $user->setBookLimit($request->get('booklimit'));
            $entityManager->persist($user);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "User added successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
                "msg" => $e->getMessage()
            ];
            return $this->response($data, 422);
        }

    }


    /**
     * @param UserRepository $userRepository
     * @param $id
     * @return JsonResponse
     * @Route("/users/{id}", name="users_get", methods={"GET"})
     */
    public function getUserData(UserRepository $userRepository, $id){
        $user = $userRepository->find($id);

        if (!$user){
            $data = [
                'status' => 404,
                'errors' => "User not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($user);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param $id
     * @return JsonResponse
     * @Route("/users/{id}", name="users_put", methods={"PUT"})
     */
    public function updateUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $user = $userRepository->find($id);

            if (!$user){
                $data = [
                    'status' => 404,
                    'errors' => "User not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);



            $user->setEmail($request->get('email'));
            if($request->get('plainPassword'))
            {
                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $request->get('plainPassword'))
                );
            }
            $user->setBookLimit($request->get('booklimit'));

            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "User updated successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }


    /**
     * @param UserRepository $userRepository
     * @param $id
     * @return JsonResponse
     * @Route("/users/{id}", name="users_delete", methods={"DELETE"})
     */
    public function deleteUser(EntityManagerInterface $entityManager, UserRepository $userRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $userRepository->find($id);

        if (!$user){
            $data = [
                'status' => 404,
                'errors' => "Post not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "User deleted successfully",
        ];
        return $this->response($data);
    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

}