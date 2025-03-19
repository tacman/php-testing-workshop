<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\DoctrineHealthSpecialistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListHealthSpecialistsController extends AbstractController
{
    public function __construct(
        private readonly DoctrineHealthSpecialistRepository $healthSpecialistRepository,
    ) {
    }

    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    #[Route(
        path: '/health-specialists',
        name: 'app_health_specialist_list',
        defaults: ['section' => 'health_specialist'],
        methods: ['GET']
    )]
    public function __invoke(): Response
    {
        // TODO: refactor with a criteria object

        $healthSpecialists = $this->healthSpecialistRepository->findBy(
            criteria: [],
            orderBy: ['specialty' => 'ASC', 'firstName' => 'ASC', 'lastName' => 'ASC'],
        );

        // TODO: refactor within a dedicated service object

        $specialties = [];
        foreach ($healthSpecialists as $healthSpecialist) {
            $specialties[$healthSpecialist->getSpecialty()->getLabel()][] = $healthSpecialist;
        }

        return $this->render('health_specialist/list.html.twig', [
            'specialties' => $specialties,
        ]);
    }
}
