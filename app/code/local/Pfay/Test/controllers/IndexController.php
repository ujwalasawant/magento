<?php
class Pfay_Test_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction ()
    {

        $this->loadLayout();
        $this->renderLayout();
    }
    public function saveAction()
    {
        //on recuperes les données envoyées en POST
        $nom = ''.$this->getRequest()->getPost('nom');
        $prenom = ''.$this->getRequest()->getPost('prenom');
        $telephone = ''.$this->getRequest()->getPost('telephone');
        //on verifie que les champs ne sont pas vide
        if(isset($nom)&&($nom!='') && isset($prenom)&&($prenom!='')
            && isset($telephone)&&($telephone!='') )
        {
            //on cree notre objet et on l'enregistre en base
            $contact = Mage::getModel('test/test');
            $contact->setData('nom', $nom);
            $contact->setData('prenom', $prenom);
            $contact->setData('telephone', $telephone);
            $contact->save();
        }
        //on redirige l’utilisateur vers la méthode index du controller indexController
        //de notre module <strong>test</strong>
        $this->_redirect('test/index/index');
    }
}