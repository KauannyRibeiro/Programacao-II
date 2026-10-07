<?php
/*
 * Aluna: 448187 Kauanny Ribeiro da Silva
 * Programação II - Lista de Exercícios: Encapsulamento (Unidade I)
 *
 * MINI GUIA (pessoal. O que cada palavra significa):
 *  class      -> molde de um objeto (ex: Carro)
 *  new        -> cria um objeto a partir da classe
 *  $this      -> "este próprio objeto" (usado dentro da classe)
 *  ->         -> acessa algo do objeto (atributo ou método)
 *  public     -> qualquer um pode acessar
 *  private    -> só a própria classe acessa
 *  protected  -> a classe e suas filhas (extends) acessam
 *  getX/setX  -> métodos para ler (get) e alterar (set) um atributo privado
 *  return     -> devolve um resultado do método
 *
 */

// =====================================================
// Exercício 1 – ContaBancaria (saldo nunca negativo)
// COMO FUNCIONA: O saldo é private: ninguém mexe nele de fora. 
// Só depositar() e sacar() alteram, e sacar() recusa se o valor for maior que o saldo, então ele nunca fica negativo.
// =====================================================
echo "\n=== Exercício 1 – ContaBancaria (saldo nunca negativo) ===\n";

class ContaBancaria {
    private $saldo = 0;

    public function depositar($valor) {
        if ($valor > 0) {
            $this->saldo += $valor;   // += soma ao saldo atual
            return true;
        }
        return false;
    }

    public function sacar($valor) {
        if ($valor > 0 && $valor <= $this->saldo) {
            $this->saldo -= $valor;   // -= subtrai do saldo atual
            return true;
        }
        return false;                 // saque negado
    }

    public function getSaldo() {
        return $this->saldo;
    }
}

$conta = new ContaBancaria();
$conta->depositar(200);
$conta->sacar(50);
echo "Saldo: R$ " . $conta->getSaldo() . "\n";          // 150
echo "Sacar 1000: " . ($conta->sacar(1000) ? "ok" : "negado") . "\n";

// =====================================================
// Exercício 2 – Produto (preço privado, sem valor negativo)
// COMO FUNCIONA: O preço é private e só muda pelo setPreco(), que rejeita valores negativos. 
// O getPreco() serve só para consultar.
// =====================================================
echo "\n=== Exercício 2 – Produto (preço privado, sem valor negativo) ===\n";

class Produto {
    private $preco;

    public function getPreco() {
        return $this->preco;
    }

    public function setPreco($preco) {
        if ($preco >= 0) {
            $this->preco = $preco;
            return true;
        }
        return false;
    }
}

$produto = new Produto();
$produto->setPreco(25.90);
echo "Preço: R$ " . $produto->getPreco() . "\n";
echo "Preço -5: " . ($produto->setPreco(-5) ? "aceito" : "recusado") . "\n";

// =====================================================
// Exercício 3 – Cliente (nome e CPF privados, CPF validado)
// COMO FUNCIONA: Nome e CPF são private. 
// O setCpf() só guarda o valor se o método privado validarCpf() confirmar que o CPF é válido (confere os dígitos verificadores).
// =====================================================
echo "\n=== Exercício 3 – Cliente (nome e CPF privados, CPF validado) ===\n";

class Cliente {
    private $nome;
    private $cpf;

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setCpf($cpf) {
        if ($this->validarCpf($cpf)) {
            $this->cpf = $cpf;
            return true;
        }
        return false;
    }

    public function getCpf() {
        return $this->cpf;
    }

    // Método privado: uso interno da classe
    private function validarCpf($cpf) {
        $cpf = preg_replace('/\D/', '', $cpf);   // tira pontos e traço
        if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += $cpf[$i] * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ($cpf[$t] != $digito) {
                return false;
            }
        }
        return true;
    }
}

$cliente = new Cliente();
$cliente->setNome("Kauanny");
echo "Nome: " . $cliente->getNome() . "\n";
echo "CPF válido (529.982.247-25): " . ($cliente->setCpf("529.982.247-25") ? "aceito" : "recusado") . "\n";
echo "CPF inválido (111.111.111-11): " . ($cliente->setCpf("111.111.111-11") ? "aceito" : "recusado") . "\n";

// =====================================================
// Exercício 4 – Usuario (senha privada e verificarSenha)
// COMO FUNCIONA: A senha é private e guardada como hash (embaralhada), nunca em texto puro. 
// A única forma de conferir é verificarSenha(), que devolve true ou false.
// =====================================================
echo "\n=== Exercício 4 – Usuario (senha privada e verificarSenha) ===\n";

class Usuario {
    private $senha;

    public function __construct($senha) {          // roda ao criar o objeto
        $this->senha = password_hash($senha, PASSWORD_DEFAULT);
    }

    public function verificarSenha($senhaDigitada) {
        return password_verify($senhaDigitada, $this->senha);   // true ou false
    }
}

$usuario = new Usuario("kau1234");
echo "Senha certa: " . ($usuario->verificarSenha("kau1234") ? "true" : "false") . "\n";
echo "Senha errada: " . ($usuario->verificarSenha("abc") ? "true" : "false") . "\n";

// =====================================================
// Exercício 5 – Funcionario e Gerente (salário protegido e bônus)
// COMO FUNCIONA: O salário é protected: a classe filha Gerente enxerga e altera, mas o código de fora não. 
// O Gerente define o bônus somando ao salário.
// =====================================================
echo "\n=== Exercício 5 – Funcionario e Gerente (salário protegido e bônus) ===\n";

class Funcionario {
    protected $salario;

    public function __construct($salario) {
        $this->salario = $salario;
    }

    public function getSalario() {
        return $this->salario;
    }
}

class Gerente extends Funcionario {      // extends = "herda de"
    public function definirBonus($bonus) {
        if ($bonus > 0) {
            $this->salario += $bonus;    // acessa o protected do pai
            return true;
        }
        return false;
    }
}

$gerente = new Gerente(5000);
$gerente->definirBonus(800);
echo "Salário com bônus: R$ " . $gerente->getSalario() . "\n";   // 5800
// echo $gerente->salario;   // ERRO: protegido, não dá para acessar de fora

// =====================================================
// Exercício 6 – Pedido (itens privados)
// COMO FUNCIONA: O array de itens é private, então só entra item por adicionarItem() e só se vê a lista por listarItens().
// =====================================================
echo "\n=== Exercício 6 – Pedido (itens privados) ===\n";

class Pedido {
    private $itens = [];

    public function adicionarItem($item) {
        $this->itens[] = $item;          // [] adiciona no fim do array
    }

    public function listarItens() {
        foreach ($this->itens as $i => $item) {
            echo ($i + 1) . " - $item\n";
        }
    }
}

$pedido = new Pedido();
$pedido->adicionarItem("Caneta");
$pedido->adicionarItem("Caderno da Kau");
$pedido->adicionarItem("Mochila");
$pedido->listarItens();

// =====================================================
// Exercício 7 – Carro (velocidade de 0 a 200 km/h)
// COMO FUNCIONA: A velocidade é private. 
// acelerar() e frear() usam min() e max() para que ela nunca passe de 200 nem fique abaixo de 0.
// =====================================================
echo "\n=== Exercício 7 – Carro (velocidade de 0 a 200 km/h) ===\n";

class Carro {
    private $velocidade = 0;

    public function acelerar($valor) {
        if ($valor > 0) {
            $this->velocidade = min(200, $this->velocidade + $valor);   // trava em 200
        }
    }

    public function frear($valor) {
        if ($valor > 0) {
            $this->velocidade = max(0, $this->velocidade - $valor);     // trava em 0
        }
    }

    public function getVelocidade() {
        return $this->velocidade;
    }
}

$carro = new Carro();
$carro->acelerar(150);
$carro->acelerar(100);   // passaria de 200, então fica em 200
echo "Velocidade: " . $carro->getVelocidade() . " km/h\n";
$carro->frear(300);      // passaria de 0, então fica em 0
echo "Velocidade: " . $carro->getVelocidade() . " km/h\n";

// =====================================================
// Exercício 8 – Aluno (nota entre 0 e 10)
// COMO FUNCIONA: Nome e nota são private. 
// O setNota() só aceita valores de 0 a 10 e informa com true/false se alterou.
// =====================================================
echo "\n=== Exercício 8 – Aluno (nota entre 0 e 10) ===\n";

class Aluno {
    private $nome;
    private $nota;

    public function __construct($nome, $nota) {
        $this->nome = $nome;
        $this->setNota($nota);
    }

    public function setNota($nota) {
        if ($nota >= 0 && $nota <= 10) {
            $this->nota = $nota;
            return true;
        }
        return false;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getNota() {
        return $this->nota;
    }
}

$aluno = new Aluno("Kauanny", 8.5);
echo $aluno->getNome() . ": " . $aluno->getNota() . "\n";
echo "Nota 11: " . ($aluno->setNota(11) ? "aceita" : "recusada") . "\n";
echo "Nota 9.5: " . ($aluno->setNota(9.5) ? "aceita" : "recusada") . "\n";
echo $aluno->getNome() . ": " . $aluno->getNota() . "\n";

// =====================================================
// Exercício 9 – Config e subclasse (parâmetros protegidos)
// COMO FUNCIONA: Os parâmetros são protected: só a classe e suas filhas acessam. 
// A subclasse ConfigAdmin consegue alterar e exibir, e o código de fora não mexe direto.
// =====================================================
echo "\n=== Exercício 9 – Config e subclasse (parâmetros protegidos) ===\n";

class Config {
    protected $parametros = [];

    public function __construct($parametros) {
        $this->parametros = $parametros;
    }
}

class ConfigAdmin extends Config {
    public function alterar($chave, $valor) {
        $this->parametros[$chave] = $valor;
    }

    public function exibir() {
        foreach ($this->parametros as $chave => $valor) {
            echo "$chave = $valor\n";
        }
    }
}

$config = new ConfigAdmin(["idioma" => "pt-BR", "autor" => "Kauanny"]);
$config->alterar("idioma", "en-US");
$config->exibir();

// =====================================================
// Exercício 10 – CarrinhoDeCompras (total privado)
// COMO FUNCIONA: O total é private. 
// Só entra valor por adicionarValor() (que recusa valores zero ou negativos) e só se consulta por exibirTotal().
// =====================================================
echo "\n=== Exercício 10 – CarrinhoDeCompras (total privado) ===\n";

class CarrinhoDeCompras {
    private $total = 0;

    public function adicionarValor($valor) {
        if ($valor > 0) {
            $this->total += $valor;
            return true;
        }
        return false;
    }

    public function exibirTotal() {
        echo "Total acumulado: R$ " . number_format($this->total, 2, ',', '.') . "\n";
    }
}

$carrinho = new CarrinhoDeCompras();
$carrinho->adicionarValor(25.5);
$carrinho->adicionarValor(100);
$carrinho->adicionarValor(-10);   // ignorado
$carrinho->exibirTotal();
