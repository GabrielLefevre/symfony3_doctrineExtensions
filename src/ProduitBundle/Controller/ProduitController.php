<?php

namespace ProduitBundle\Controller;

use ProduitBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Produit controller.
 *
 */
class ProduitController extends Controller
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('ProduitBundle:Produit')->findAll();

        return $this->render('produit/index.html.twig', array(
            'produits' => $produits,
        ));
    }


    public function showAction(Produit $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);
        return $this->render('produit/show.html.twig', array(
            'produit' => $produit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function newAction(Request $request)
    {
        $produit = new Produit();
        $form = $this->createForm('ProduitBundle\Form\ProduitType', $produit, array(
            'data_class' => 'ProduitBundle\Entity\Produit'));
        if($request->getMethod()=== "POST") {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($produit);
                $em->flush($produit);

                return $this->redirectToRoute('produit_index', array());
            }
        }
        return $this->render('produit/new.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Request $request, Produit $produit)
    {
        $form = $this->createDeleteForm($produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush($produit);
        }
        return $this->redirectToRoute('produit_index');
    }

    private function createDeleteForm(Produit $produit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produit_delete', array('id' => $produit->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
