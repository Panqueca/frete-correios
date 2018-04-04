<?php
    $cepEnvio = "80230040";
    $cepDestino = "89201002";
    $declararValor = false;

    $data['nCdEmpresa'] = '';
    $data['sDsSenha'] = '';
    $data['sCepOrigem'] = $cepEnvio;
    $data['sCepDestino'] = $cepDestino;
    $data['nVlPeso'] = '.5';
    $data['nCdFormato'] = '1';
    $data['nVlComprimento'] = '32';
    $data['nVlAltura'] = '10';
    $data['nVlLargura'] = '30';
    $data['nVlDiametro'] = '0';
    $data['sCdMaoPropria'] = 'n';
    $data['nVlValorDeclarado'] = $declararValor == true ? '341' : '0';
    $data['sCdAvisoRecebimento'] = 'n';
    $data['StrRetorno'] = 'xml';
    $data['nCdServico'] = '41106';
    $data = http_build_query($data);

    $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';

    $curl = curl_init($url . '?' . $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);
    $result = simplexml_load_string($result);
    foreach($result -> cServico as $row) {
        //Os dados de cada serviço estará aqui
        if($row -> Erro == 0) {
            //echo $row -> Codigo . '<br>';
            echo "Preco: {$row -> Valor} <br>";
            echo "Prazo entrega: {$row -> PrazoEntrega} dias<br>";
            $frete = array("valor" => $row->Valor, "prazo" => $row->PrazoEntrega);
        }else{
            echo $row -> MsgErro;
        }
    }