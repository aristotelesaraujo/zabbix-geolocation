<?php

// funcao que carrega as classes automaticamente 
function __autoload($classe) { 

    //busca dentro da pasta classes a classe necessaria... 
    include "class/{$classe}.class.php"; 
 
}

?>