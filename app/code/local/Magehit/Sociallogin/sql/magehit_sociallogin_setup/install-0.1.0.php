<?php

$installer = $this;

$installer->startSetup();



$installer->setSocialCustomerAttributes(

    array(

        'magehit_sociallogin_gid' => array(
            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_gid",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""           

        ),            

        'magehit_sociallogin_gtoken' => array(

            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_gtoken",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""          

        ),

        'magehit_sociallogin_fid' => array(

            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_fid",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""          

        ),            

        'magehit_sociallogin_ftoken' => array(

            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_ftoken",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""          

        ), 

		'magehit_sociallogin_tid' => array(

         
            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_tid",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""      

        ),            

        'magehit_sociallogin_ttoken' => array(

           
            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_ttoken",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""               

        ),     
        'magehit_sociallogin_lid' => array(

           
            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_lid",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""               

        ),   
        'magehit_sociallogin_ltoken' => array(

           
            "type"     => "text",
            "backend"  => "",
            "label"    => "magehit_sociallogin_ltoken",
            "input"    => "text",
            "source"   => "",
            "visible"  => false,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""               

        ),        

    )

);



// Install our custom attributes

$installer->installSocialCustomerAttributes();

//$installer->removeSocialCustomerAttributes();

$installer->endSetup();