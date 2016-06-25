<?php

$installer = $this;

$installer->startSetup();



$installer->setSocialCustomerAttributes(

    array(

        'magehit_sociallogin_igid' => array(
            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_igid",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""           

        ),            

        'magehit_sociallogin_igtoken' => array(

            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_igtoken",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""          

       )        

    )

);



// Install our custom attributes

$installer->installSocialCustomerAttributes();

//$installer->removeSocialCustomerAttributes();

$installer->endSetup();