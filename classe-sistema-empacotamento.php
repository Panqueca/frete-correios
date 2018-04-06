<?php
    class Empacotamento{
        public $caixas = array();
        public $produtos = array();
        public $produtos_configurados = 0;
        public $produtos_empacotados = array();
        public $ctrl_produtos_empacotados = 0;
        
        public function add_produto($id, $title, $preco, $comprimento, $largura, $altura, $peso_produto){
            $this->produtos[$this->produtos_configurados] = array();
            $this->produtos[$this->produtos_configurados]["id"] = $id;
            $this->produtos[$this->produtos_configurados]["title"] = $title;
            $this->produtos[$this->produtos_configurados]["preco"] = $preco;
            $this->produtos[$this->produtos_configurados]["comprimento"] = $this->valida_dimensao($comprimento);
            $this->produtos[$this->produtos_configurados]["largura"] = $this->valida_dimensao($largura);
            $this->produtos[$this->produtos_configurados]["altura"] = $this->valida_dimensao($altura);
            $this->produtos[$this->produtos_configurados]["peso"] = $this->valida_dimensao($peso_produto);
            $this->produtos[$this->produtos_configurados]["empacotado"] = false;            
            $this->produtos_configurados++;
        }
        
        public function configurar(){
            $carrinho = array();
            $ctrl = 0;
            foreach($this->produtos as $indice => $infoProduto){
                $empacotado = $infoProduto["empacotado"];
                if($empacotado == false){
                    $carrinho[$ctrl] = array();
                    $carrinho[$ctrl]["title"] = $infoProduto["title"];
                    $carrinho[$ctrl]["id"] = $infoProduto["id"];
                    $carrinho[$ctrl]["peso"] = $infoProduto["peso"];
                    $carrinho[$ctrl]["preco"] = $infoProduto["preco"];
                    $carrinho[$ctrl]["A"] = $infoProduto["altura"];
                    $carrinho[$ctrl]["L"] = $infoProduto["largura"];
                    $carrinho[$ctrl]["C"] = $infoProduto["comprimento"];
                    $carrinho[$ctrl]["empacotado"] = false;
                    
                    $ctrl++;
                }
            }
            return $carrinho;
        }
        
        public function valida_dimensao($val, $sep = "."){
            $prepareStr = str_replace(" ", "", $val);
            $prepareStr = str_replace(",", ".", $prepareStr);
            $totalCaracteres = strlen($prepareStr);
            $cleanedVal = floatval(str_replace(".", "", $prepareStr));
            $temPonto = strlen($cleanedVal) < $totalCaracteres ? true : false;
            if($temPonto){
                $explodedVal = explode(".", $prepareStr);
                $totalExplodes = count($explodedVal);
                $indiceLastExplode = $totalExplodes - 1;
                $decimal = strlen($explodedVal[$indiceLastExplode]) <= 2 && strlen($explodedVal[$indiceLastExplode]) > 0 ? true : false;
                $shortDecimal = strlen($explodedVal[$indiceLastExplode]) == 1 ? true : false;
                $startingVal = $explodedVal[0];
                
                $caracteresStrCleaned = strlen($cleanedVal);
                $totalCaractesMilhar = $caracteresStrCleaned - 2;
                $milharVal = substr($cleanedVal, 0, $totalCaractesMilhar);
                $decimalsVal = substr($cleanedVal, $totalCaractesMilhar, 2);
                
                $sepStartVal = preg_split("//", $startingVal, -1, PREG_SPLIT_NO_EMPTY);
                $somaStart = 0;
                foreach($sepStartVal as $number){
                    $somaStart += $number;
                }
                $is_under_one = $somaStart == 0 ? true : false;
                
                $sep = $sep == "." || $sep ==  "," ? $sep : ".";
                if($is_under_one){
                    $formatedVal = "0".$sep.$cleanedVal;
                }else{
                    switch($decimal){
                        case true:
                            if($shortDecimal){
                                $ctrlCaracteres = strlen($cleanedVal);
                                $formatedVal = substr($cleanedVal, 0, $ctrlCaracteres - 1) . $sep . $explodedVal[$totalExplodes - 1];
                            }else{
                                $formatedVal = $milharVal.$sep.$decimalsVal;
                            }
                            break;
                        case false:
                            $formatedVal = $cleanedVal.$sep."00";
                            break;
                    }
                }
                
                return $formatedVal;
            }else{
                return $cleanedVal;
            }
        }
    }
?>