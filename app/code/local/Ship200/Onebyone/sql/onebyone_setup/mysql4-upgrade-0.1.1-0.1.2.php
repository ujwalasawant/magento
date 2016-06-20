<?php
$this->startSetup();
$this->addAttribute('order', 'ship200_tack', array(
    'type'          => 'varchar',
    'label'         => 'Ship200 Tracking Code',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
        'user_defined'  =>  true
));
 
$this->endSetup();
?>