<?php

namespace ProduitBundle\Controller;

use ProduitBundle\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategorieController extends Controller
{


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('ProduitBundle:Categorie');
        $categories = $repo->findAll();

        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a href="/cat/'.$node['slug'].'">'.$node['slug'].'</a>';
            }
        );
        $arrayTree = $repo->childrenHierarchy(null,false,$options);

        return $this->render('categorie/index.html.twig', array(
            'categories' => $categories,
            'arrayTree' => $arrayTree,
        ));
    }


    public function showAction(Categorie $categorie)
    {
        $deleteForm = $this->createDeleteForm($categorie);
        return $this->render('categorie/show.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categorie = new Categorie();
        $form = $this->createForm('ProduitBundle\Form\CategorieType', $categorie, array(
            'data_class' => 'ProduitBundle\Entity\Categorie',
            'em' => $em));
        if($request->getMethod()=== "POST") {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($categorie);
                $em->flush($categorie);

                return $this->redirectToRoute('categorie_index', array());
            }
        }
        return $this->render('categorie/new.html.twig', array(
            'categorie' => $categorie,
            'form' => $form->createView(),
        ));
    }
}
