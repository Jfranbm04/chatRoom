<?php

namespace App\Controller;

use App\Entity\ChatGroup;
use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\ChatGroupRepository;
use App\Repository\MessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(ChatGroupRepository $chatGroupRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Lista de todos los mensajes
        $chatGroups = $chatGroupRepository->findChatGroupsWithUsers();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'chatGroups' => $chatGroups,
        ]);
    }

    // Funcion para crear un grupo nuevo
    #[Route('/newGroup', name: 'newGroup', methods: ['GET', 'POST'])]
    public function newMessage(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chatGroup = new ChatGroup();
        $chatGroup->setName($request->get('name'));

        $entityManager->persist($chatGroup);
        $entityManager->flush();

        // Obtener el ID del nuevo grupo creado para redirigir a la página del chatGroup
        $newChatGroupId = $chatGroup->getId();

        return $this->redirectToRoute('app_chat_group', [
            'id' => $newChatGroupId
        ]);
    }
}
