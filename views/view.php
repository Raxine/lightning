<?php

require $CFG->dirmodelroot . 'form.php';


$form = new form('Test');

$form->add('text', 'username')
     ->required(true)
     ->autocomplete(false)
     ->placeholder('Nom d\'utilisateur');


$form->add('email', 'user_email')
     ->label('Votre email')
     ->required(true);

$form->add('tel', 'tel')
     ->required(false);

$form->add('submit', 'submit')
     ->value('Envoyer !!');


if($datas = $form->getDatas()) {
    var_dump($datas);
} else {
    $form->display();
}