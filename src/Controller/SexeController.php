<?php

namespace App\Controller;

use App\Entity\Sexe;
use App\Repository\SexeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SexeController extends AbstractController
{
    private $manager;

    private $sexeRepository;

    public function __construct(EntityManagerInterface $manager, SexeRepository $sexeRepository)
    {
        $this->sexeRepository = $sexeRepository;
        $this->manager = $manager;
    }

    #[Route('/create_sexe', name: 'create_sexe', methods:'POST')]
    public function createSexe(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];

        $is_sexe_exist = $this->sexeRepository->findBy(['name'=>$name]);

        if($is_sexe_exist){
            return new JsonResponse([
                'status'=> false,
                'message'=> 'The sexe is already exist, Please, Enter another sexe!'
            ]);
        }else{
            $sexe = new Sexe();

            $sexe->setName($name);
            $this->manager->persist($sexe);
            $this->manager->flush();

            return new JsonResponse([
                'status'=>true,
                'message'=>'Genre cree avec succes'
            ]);
        }

        
        
    }

    #[Route('/get_all_sexe', name: 'get_all_sexe', methods:'GET')]
    public function getAllSexe(): Response
    {
        $sexes = $this->sexeRepository->findAll();

        if (!empty($sexes)) {
            return $this->json($sexes, 200);
        }else{
            return new JsonResponse([
                'status'=>false,
                'message'=>'No data found in database'
            ]);
        }
    
        
    }

    #[Route('/update_sexe/{id}', name:'update_sexe', methods:'PUT' )]
    public function updateSexe(Request $request, $id): Response
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];

        $is_sexe_find = $this->sexeRepository->find($id);

        if ($is_sexe_find) {
            $is_sexe_find->setName($name);
            $this->manager->persist($is_sexe_find);
            $this->manager->flush();

            return new JsonResponse([
                'status'=>true,
                'message'=> 'Sexe updated successfully'
            ]);
        }else{
            return new JsonResponse([
                'status'=>false,
                'message'=>'That sexe does not exist, Please, Enter a exist sexe!'
            ]);
        }


    }

    #[Route('/delete_sexe/{id}', name:'delete_sexe', methods:'DELETE')]
    public function deleteSexe(Sexe $sexe): Response
    {
        $sexe_to_delete = $this->sexeRepository->find($sexe);

        $this->manager->remove($sexe);
        $this->manager->flush();

        return new JsonResponse([
            'status'=>true,
            'message'=> 'Sexe deleted successfully'
        ]);
    }
}
