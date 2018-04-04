<?php
require 'cls_session.class.php';
$session = new Cls_session();

define("MIN_LARGURA", 11);
define("MAX_LARGURA", 105);

define("MIN_ALTURA", 2);
define("MAX_ALTURA", 105);

define("MIN_COMPRIMENTO", 16);
define("MAX_COMPRIMENTO", 105);

define("MIN_SOMA_CLA", 29);
define("MAX_SOMA_CLA", 200);


function ordenar_carrinho($ordened_carrinho = null)
{
    if(!is_array($ordened_carrinho)) return false;

        foreach ($ordened_carrinho as $k => $infoProduto){
        $new_altura = min( $infoProduto['A'], $infoProduto['L'], $infoProduto['C'] );
        $new_comprimento = max( $infoProduto['A'], $infoProduto['L'], $infoProduto['C'] );
        $_new_largura = array( $infoProduto['A'], $infoProduto['L'], $infoProduto['C'] );
            
        sort($_new_largura);
        array_shift($_new_largura);
        array_pop($_new_largura);

        $infoProduto['L'] = isset($_new_largura[0]) ? $_new_largura[0] : $new_altura;
        $infoProduto['A'] = $new_altura;
        $infoProduto['C'] = $new_comprimento;
        $infoProduto['LC'] = $infoProduto['L'] * $infoProduto['C'];
        $ordened_carrinho[$k] = $infoProduto;

    }

    function order_largura_comprimento($a, $b){
        return $a['LC'] < $b['LC'];
    }

    usort($ordened_carrinho, 'order_largura_comprimento');

    return $ordened_carrinho;
}

$caixas = array();
$ctrl_caixas = 0;
function set_caixa(){
    global $ctrl_caixas;
    
    $caixa = array(
        'altura' => 0,
        'largura' => 0,
        'comprimento' => 0,
        'qtd_itens' => 0,
        'message' => null,
        'volume' => 0,
        'volume_itens' => 0,
        'volume_vazio' => 0,
        'comprimento_remanescente' => 0,
        'largura_remanescente' => 0,
        'altura_remanescente' => 0
    );
    
    $ctrl_caixas++;
    return $caixa;
}

function calcular_caixas($carrinho = null)
{
    global $ctrl_caixas;

    if(!is_array($carrinho)) return false;

    $carrinho = ordenar_carrinho($carrinho);
    
    $newCaixa = set_caixa();

    $caixas[$ctrl_caixas] = json_decode(json_encode($newCaixa, FALSE));

    if(empty($carrinho)){
        return false;
    }

    foreach($carrinho as $indice => $infoProduto){
        if($infoProduto["empacotado"] == true){
            echo "<br>Produto já esta empacotado<br>";
            continue;
        }
        echo "<br>Produto está sendo empacotado<br>";
        
        $caixas[$ctrl_caixas]->qtd_itens++; // Itens na caixa

        $caixas[$ctrl_caixas]->volume_itens += ( $infoProduto['A']*$infoProduto['L']*$infoProduto['C'] );

        // verifica se produto cabe na caixa
        if($caixas[$ctrl_caixas]->comprimento_remanescente >= $infoProduto['C'] && $caixas[$ctrl_caixas]->largura_remanescente >= $infoProduto['L']){

            if($infoProduto['A'] > $caixas[$ctrl_caixas]->altura_remanescente){
                $caixas[$ctrl_caixas]->altura += $infoProduto['A'] - $caixas[$ctrl_caixas]->altura_remanescente;
            }

            if($infoProduto['C'] > $caixas[$ctrl_caixas]->comprimento){
                $caixas[$ctrl_caixas]->comprimento = $infoProduto['C'];
            }

            $caixas[$ctrl_caixas]->comprimento_remanescente = $caixas[$ctrl_caixas]->comprimento - $infoProduto['C'];

            $caixas[$ctrl_caixas]->largura_remanescente = $caixas[$ctrl_caixas]->largura_remanescente - $infoProduto['L'];

            $caixas[$ctrl_caixas]->altura_remanescente = $infoProduto['A'] > $caixas[$ctrl_caixas]->altura_remanescente ? $infoProduto['A'] : $caixas[$ctrl_caixas]->altura_remanescente;
            $__['side'] = true;
            
            continue;
        }

        // passo (N-1) - altura é a variavel que sempre incrementa independente de condicao ...
        $caixas[$ctrl_caixas]->altura += $infoProduto['A'];

        // passo N - verificando se item tem dimensoes maiores que a caixa...
        if( $infoProduto['L'] > $caixas[$ctrl_caixas]->largura){
            $caixas[$ctrl_caixas]->largura = $infoProduto['L'];
        }

        if($infoProduto['C'] > $caixas[$ctrl_caixas]->comprimento){
            $caixas[$ctrl_caixas]->comprimento = $infoProduto['C'];
        }

        // calcular volume restante
        $caixas[$ctrl_caixas]->comprimento_remanescente = $caixas[$ctrl_caixas]->comprimento;
        $caixas[$ctrl_caixas]->largura_remanescente = $caixas[$ctrl_caixas]->largura - $infoProduto['L'];
        $caixas[$ctrl_caixas]->altura_remanescente = $infoProduto['A'];        
    }

    // Recalculando volume da caixa
    $caixas[$ctrl_caixas]->volume = ($caixas[$ctrl_caixas]->altura * $caixas[$ctrl_caixas]->largura * $caixas[$ctrl_caixas]->comprimento);

    // Espaço restante na caixa
    $caixas[$ctrl_caixas]->volume_vazio = $caixas[$ctrl_caixas]->volume - $caixas[$ctrl_caixas]->volume_itens;

    if(!empty($carrinho)){        
        // Validando obrigações das dimensões da caixa
        if($caixas[$ctrl_caixas]->altura > 0 && $caixas[$ctrl_caixas]->altura < MIN_ALTURA) $caixas[$ctrl_caixas]->altura = MIN_ALTURA;
        if($caixas[$ctrl_caixas]->largura > 0 && $caixas[$ctrl_caixas]->largura < MIN_LARGURA) $caixas[$ctrl_caixas]->largura = MIN_LARGURA;
        if($caixas[$ctrl_caixas]->comprimento > 0 && $caixas[$ctrl_caixas]->comprimento < MIN_COMPRIMENTO) $caixas[$ctrl_caixas]->comprimento = MIN_COMPRIMENTO;
    }

    // Validando limites das caixas
    if($caixas[$ctrl_caixas]->altura > MAX_ALTURA){
        return false;
    }
    if($caixas[$ctrl_caixas]->largura > MAX_LARGURA){
        return false;
    }
    if($caixas[$ctrl_caixas]->comprimento > MAX_COMPRIMENTO){
        return false;
    }

    if(($caixas[$ctrl_caixas]->comprimento+$caixas[$ctrl_caixas]->comprimento+$caixas[$ctrl_caixas]->comprimento) < MIN_SOMA_CLA){        
        return false;
    }

    if(($caixas[$ctrl_caixas]->comprimento+$caixas[$ctrl_caixas]->comprimento+$caixas[$ctrl_caixas]->comprimento) > MAX_SOMA_CLA){
        return false;
    }
    
    $infoProduto["empacotado"] = true;
    
    return $caixas[$ctrl_caixas];
}

?>