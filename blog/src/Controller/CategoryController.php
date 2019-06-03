<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     * @param Request $request
     * @return Response A response
     * @IsGranted("ROLE_ADMIN")
     */
    public function addNewCategory(Request $request): Response
    {
        $category = new Category();
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data= $form->getData();
            $categoryManager = $this->getDoctrine()->getManager();
            $categoryManager->persist($data);
            $categoryManager->flush();
            return $this->redirectToRoute('category');
        }
        $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
        return $this->render('category/index.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }
}
