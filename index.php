<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Sistema de empacotamento</title>

    </head>
    <body>
        <?php
        require_once "@classe-sistema-empacotamento.php";
        require_once "@calculo-caixas.php";
        echo "<pre>";
        $servicoCorreios = "41106";
        $empacotamento = new Empacotamento();
        echo "<br><br><br>";
        $empacotamento->add_produto("Bolsa maidigrey", "#rb12", 10, 30, 32, ".3");
        echo "<br><br><br>";
        $carrinho = $empacotamento->configurar();

        $box = calcular_caixas($carrinho);
        
        if($box != false){
            // resultado final
            echo "<div class='infos'>
                <br><b> Dimensções da Caixa </b> <br>
                Altura          : {$box->altura} cm, <br>
                Largura         : {$box->largura} cm, <br>
                Comprimento     : {$box->comprimento} cm, <br>
                Itens           : {$box->qtd_itens} un, <br>
                Volume          : {$box->volume} cm2, <br>
                Volume Produtos : {$box->volume_itens} cm2, <br>
                Volume Vazio    : {$box->volume_vazio} cm2, <br>
              </div>" ;

            echo ( is_null( $box->message ) ) ? null : "Mensagem ".$box->message;
            
            $session->altura 		= $box->altura ; 
            $session->largura 		= $box->largura ; 
            $session->comprimento 	= $box->comprimento ; 
            $session->qtd_itens 	= $box->qtd_itens ; 
            $session->volume 		= $box->volume ; 
            $session->volume_itens 	= $box->volume_itens ; 
            $session->volume_vazio 	= $box->volume_vazio ;
        }


        ?>

        <style>
            body{margin:0;font-family:'Open Sans',sans-serif;color:#111;}
            h1{position: absolute;font-size: 25px;left:20px;font-weight: 100;top: 200px;}
            .infos{padding:10px;position:absolute;color:#111;top:250px;left:10px;font-weight: 200;}
        </style>

        <h1>Encomenda a ser enviada via CORREIOS</h1>
    </body>
</html>