<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(User::class)
                    ->setPage($page)
                    ->setLimit(5);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination       
        ]);
    }

    /**
     * Permet de modifier un commentaire
     * @Route("/admin/users/{id}/edit", name="admin_users_edit")
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur n°<strong>{$user->getId()}</strong> a été modifié"
            );
        }

        return $this->render("admin/user/edit.html.twig",[
            'user' => $user,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {
        $this->addFlash(
            'success',
            "L'utilisateur n°{$user->getId()} a bien été supprimé"
        );
        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_users_index');
    }
}
