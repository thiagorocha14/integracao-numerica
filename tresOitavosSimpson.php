<?php
    function tresOitavosSimpson($fx,$altura){
     $resultado = 0;
      for ($i =0, $tamanho =count($fx); $i < $tamanho ; $i++) {
        if (($i == 0) || (i == ($tamanho-1))) {
            $resultado =$resultado + $fx[$i]
        }else{
            if ( ($i % 2)==0 ) {
                $resultado =$resultado + (2 * $fx[$i]);
            }else{
                $resultado =$resultado + (4 * $fx[$i]);
            } 
        }

      }
      $resultado = ($altura/3)* $resultado;

      return $resultado;
    }
    
?>