<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\RolePasswordEncoderInterface;


/**
 * Role controller.
 *
 * @Route("/api")
 */
class RoleController extends AbstractController
{

    /**
     * @Route("/roles", name="roles",methods={"GET"})
     */
    public function getRoles(Request $request): Response
    {

        $roles = $this->getDoctrine()->getRepository(Role::class)->findAll();
        return $this->json($roles);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param RoleRepository $roleRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/roles", name="roles_add", methods={"POST"})
     */
    public function addRole(Request $request, EntityManagerInterface $entityManager, RoleRepository $roleRepository){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try{
            $request = $this->transformJsonBody($request);


            $role = new Role();
            $role->setName($request->get('name'));
            $role->setRole($request->get('role'));
            $role->setAccessList($request->get('accessList'));

            $entityManager->persist($role);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Role added successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
                'msg' => $e
            ];
            return $this->response($data, 422);
        }

    }


    /**
     * @param RoleRepository $roleRepository
     * @param $id
     * @return JsonResponse
     * @Route("/roles/{id}", name="roles_get", methods={"GET"})
     */
    public function getRoleData(RoleRepository $roleRepository, $id){
        $role = $roleRepository->find($id);

        if (!$role){
            $data = [
                'status' => 404,
                'errors' => "Role not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($role);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param RoleRepository $roleRepository
     * @param $id
     * @return JsonResponse
     * @Route("/roles/{id}", name="roles_put", methods={"PUT"})
     */
    public function updateRole(Request $request, EntityManagerInterface $entityManager, RoleRepository $roleRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $role = $roleRepository->find($id);

            if (!$role){
                $data = [
                    'status' => 404,
                    'errors' => "Role not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);


            $role->setName($request->get('name'));
            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Role updated successfully",
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
     * @param RoleRepository $roleRepository
     * @param $id
     * @return JsonResponse
     * @Route("/roles/{id}", name="roles_delete", methods={"DELETE"})
     */
    public function deleteRole(EntityManagerInterface $entityManager, RoleRepository $roleRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $role = $roleRepository->find($id);

        if (!$role){
            $data = [
                'status' => 404,
                'errors' => "Role not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($role);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Role deleted successfully",
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