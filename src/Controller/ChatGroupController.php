<?php

namespace App\Controller;

use App\Entity\ChatGroup;
use App\Entity\Messages;
use App\Form\ChatGroupType;
use App\Repository\ChatGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chat/group')]
final class ChatGroupController extends AbstractController
{
    #[Route(name: 'app_chat_group_index', methods: ['GET'])]
    public function index(ChatGroupRepository $chatGroupRepository): Response
    {
        return $this->render('chat_group/index.html.twig', [
            'chat_groups' => $chatGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chat_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chatGroup = new ChatGroup();
        $form = $this->createForm(ChatGroupType::class, $chatGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chatGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_group_index', [], Response::HTTP_SEE_OTHER);
        }

        /*
        return $this->redirectToRoute('app_chat_group', [
            'chat_group' => $chatGroup,
            'form' => $form,
        ]);
        */


        return $this->render('chat_group/new.html.twig', [
            'chat_group' => $chatGroup,
            'form' => $form,
        ]);
    }






    // Funcion para crear un nuevo mensaje y añadirlo a la base de datos
    #[Route('/newMessage', name: 'newMessage', methods: ['GET', 'POST'])]
    public function newMessage(ChatGroupRepository $chatGroupRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Messages();
        $message->setUser($this->getUser());
        $message->setDate(new \DateTime());
        $message->setContent($request->get('content'));

        // Obtengo el objeto grupo segun id $chatGroupId
        $chatGroupId = $request->get('chatGroupId');
        $group = $chatGroupRepository->find($chatGroupId);
        // Añado el grupo al chat
        $message->setChatGroup($group);

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->redirectToRoute('app_chat_group', ['id' => $chatGroupId]);
    }

    // Funcion para que el usuario salga del chat
    #[Route('/cerrarChat/{id}', name: 'cerrar_chat', methods: ['GET'])]
    public function cerrarChat(ChatGroup $chatGroup, ChatGroupRepository $chatGroupRepository, EntityManagerInterface $entityManager): Response
    {


        // Añado el usuario a la lista de usuarios        
        $user = $this->getUser();
        $chatGroup->removeUser($user);

        $entityManager->persist($chatGroup);
        $entityManager->flush();

        return $this->redirectToRoute('app_main');
    }

    // Funcion para mostrar la vista chat_group.html.twig y añadir el usuario al array de usuarios activos del chatgroup
    #[Route('/{id}', name: 'app_chat_group', methods: ['GET'])]
    public function chatGroup(ChatGroup $chatGroup, ChatGroupRepository $chatGroupRepository, EntityManagerInterface $entityManager): Response
    {
        // $chatGroup = $chatGroupRepository->find($idGroup);
        $messages = $chatGroup->getMessages();

        // Añado el usuario a la lista de usuarios        
        $user = $this->getUser();
        $chatGroup->addUser($user);

        $entityManager->persist($chatGroup);
        $entityManager->flush();

        return $this->render('main/chat_group.html.twig', [
            'controller_name' => 'ChatGroupController',
            'chatGroup' => $chatGroup,
            'messages' => $messages,
        ]);
    }





    // #[Route('/{id}', name: 'app_chat_group_show', methods: ['GET'])]
    // public function show(ChatGroup $chatGroup): Response
    // {
    //     return $this->render('chat_group/show.html.twig', [
    //         'chat_group' => $chatGroup,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_chat_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChatGroup $chatGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatGroupType::class, $chatGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat_group/edit.html.twig', [
            'chat_group' => $chatGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_group_delete', methods: ['POST'])]
    public function delete(Request $request, ChatGroup $chatGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chatGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chatGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
