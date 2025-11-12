# SierraTecnologia PHP bindings

[![Build Status](https://travis-ci.org/sierratecnologia/sierratecnologia-php.svg?branch=master)](https://travis-ci.org/sierratecnologia/sierratecnologia-php)
[![Latest Stable Version](https://poser.pugx.org/sierratecnologia/sierratecnologia-php/v/stable.svg)](https://packagist.org/packages/sierratecnologia/sierratecnologia-php)
[![Total Downloads](https://poser.pugx.org/sierratecnologia/sierratecnologia-php/downloads.svg)](https://packagist.org/packages/sierratecnologia/sierratecnologia-php)
[![License](https://poser.pugx.org/sierratecnologia/sierratecnologia-php/license.svg)](https://packagist.org/packages/sierratecnologia/sierratecnologia-php)
[![Code Coverage](https://coveralls.io/repos/sierratecnologia/sierratecnologia-php/badge.svg?branch=master)](https://coveralls.io/r/sierratecnologia/sierratecnologia-php?branch=master)

---

## 📘 Introdução

### O que é a biblioteca payment-php

A **payment-php** é uma biblioteca PHP robusta e profissional desenvolvida pela **SierraTecnologia** para integração com sistemas de pagamento. Ela fornece uma camada de abstração completa para processamento de transações financeiras, gestão de clientes, assinaturas, cobranças e muito mais.

Esta biblioteca foi projetada seguindo os mais altos padrões de qualidade de código PHP, com suporte a PSR-12, análise estática com Psalm, testes automatizados e integração contínua.

### Objetivo e Filosofia do Projeto

A filosofia da **payment-php** é fornecer uma interface limpa, intuitiva e type-safe para operações de pagamento, abstraindo a complexidade das APIs de processamento de pagamento e fornecendo:

- **Simplicidade**: API fluente e intuitiva
- **Segurança**: Validações rigorosas e tratamento de erros robusto
- **Extensibilidade**: Arquitetura modular que permite customizações
- **Confiabilidade**: Cobertura de testes abrangente e CI/CD automatizado
- **Compatibilidade**: Suporte a múltiplas versões do PHP (7.4+, 8.0, 8.1, 8.2+)

### Benefícios de Uso

- ✅ **Integração Rápida**: Implementação simplificada de funcionalidades de pagamento
- ✅ **Type Safety**: Aproveitamento de type hints e análise estática
- ✅ **Testável**: Arquitetura que facilita testes unitários e de integração
- ✅ **Laravel Ready**: Integração nativa com Laravel (Service Providers, Auto-discovery)
- ✅ **Documentação Completa**: Exemplos práticos e referência de API detalhada
- ✅ **Suporte Empresarial**: Mantido profissionalmente pela SierraTecnologia

### Integração no Ecossistema SierraTecnologia

A biblioteca **payment-php** é parte fundamental do ecossistema SierraTecnologia, integrando-se perfeitamente com:

- **Módulos de Faturamento**: Gestão de invoices e billing cycles
- **CRM Corporativo**: Sincronização de dados de clientes e transações
- **Analytics e Relatórios**: Métricas de receita e performance financeira
- **Microserviços**: Comunicação via eventos e APIs REST

---

## 🚀 Instalação

You can sign up for a SierraTecnologia account at https://sierratecnologia.com.br.

### Requisitos Mínimos

- **PHP**: >= 7.4 (recomendado PHP 8.2+)
- **Extensões PHP Obrigatórias**:
  - `ext-curl`: Para comunicação HTTP
  - `ext-json`: Para serialização/deserialização de dados
  - `ext-mbstring`: Para manipulação de strings multibyte
- **Composer**: >= 2.0
- **Laravel** (opcional): >= 7.x | 8.x | 9.x | 10.x | 11.x

### Instalação via Composer

```bash
composer require sierratecnologia/payment-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

### Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/sierratecnologia/sierratecnologia-php/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/sierratecnologia-php/init.php');
```

### Dependencies

The bindings require the following extensions in order to work properly:

- [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

### Getting Started

Simple usage looks like:

```php
\SierraTecnologia\SierraTecnologia::setApiKey('sk_test_BQokikJOvBiI2HlWgH4olfQ2');
$charge = \SierraTecnologia\Charge::create(['amount' => 2000, 'currency' => 'usd', 'source' => 'tok_189fqt2eZvKYlo2CTGBeg6Uq']);
echo $charge;
```

---

## 🏗️ Arquitetura e Estrutura Interna

### Estrutura de Diretórios

```
payment-php/
├── src/
│   ├── ApiOperations/          # Traits para operações CRUD
│   ├── Checkout/               # Módulo de checkout
│   ├── Error/                  # Exceções customizadas
│   ├── HttpClient/             # Cliente HTTP
│   ├── Issuing/                # Emissão de cartões
│   ├── Radar/                  # Prevenção de fraudes
│   ├── Reporting/              # Relatórios
│   ├── Terminal/               # Terminais POS
│   ├── Util/                   # Utilitários
│   ├── SierraTecnologia.php    # Classe principal de configuração
│   ├── ApiRequestor.php        # Gerenciador de requisições HTTP
│   ├── ApiResource.php         # Classe base de recursos
│   ├── Charge.php              # Cobranças
│   ├── Customer.php            # Clientes
│   └── ...
├── tests/                      # Testes automatizados
├── examples/                   # Exemplos de uso
└── docs/                       # Documentação adicional
```

### Padrões Arquiteturais

A biblioteca utiliza os seguintes padrões de design:

1. **Active Record Pattern**: Recursos da API são representados como objetos com métodos de persistência
2. **Trait-based Composition**: Operações CRUD implementadas como traits reutilizáveis
3. **Factory Pattern**: Criação de objetos específicos baseados em tipo
4. **Strategy Pattern**: Diferentes estratégias de HTTP client
5. **Singleton Pattern**: Cliente HTTP compartilhado

### Fluxo de Comunicação

```
Aplicação → ApiResource → ApiRequestor → HttpClient → API SierraTecnologia
                ↓              ↓             ↓
            Validação    Autenticação   Retry Logic
```

---

## 🔧 Principais Componentes e Responsabilidades

### 1. Classe SierraTecnologia (src/SierraTecnologia.php:1)

Classe principal de configuração global.

**Exemplo de Uso**:

```php
use SierraTecnologia\SierraTecnologia;

// Configuração básica
SierraTecnologia::setApiKey('sk_test_...');
SierraTecnologia::setApiVersion('2023-10-16');

// Configurações avançadas
SierraTecnologia::setMaxNetworkRetries(3);
SierraTecnologia::setEnableTelemetry(true);
SierraTecnologia::setAppInfo('MeuApp', '1.0.0', 'https://meuapp.com');
```

### 2. Charge (src/Charge.php:1)

Representa uma cobrança/transação.

**Exemplo Prático**:

```php
use SierraTecnologia\Charge;

// Criar uma cobrança
$charge = Charge::create([
    'amount' => 2000, // R$ 20,00 em centavos
    'currency' => 'brl',
    'source' => 'tok_visa',
    'description' => 'Cobrança de teste'
]);

// Capturar cobrança
$charge->capture();

// Reembolsar
$charge->refund(['amount' => 1000]); // Reembolso parcial
```

### 3. Customer (src/Customer.php:1)

Representa um cliente.

**Exemplo Prático**:

```php
use SierraTecnologia\Customer;

// Criar cliente
$customer = Customer::create([
    'email' => 'joao@example.com',
    'name' => 'João Silva',
    'phone' => '+5511999999999'
]);

// Adicionar método de pagamento
Customer::createSource($customer->id, ['source' => 'tok_visa']);

// Listar faturas do cliente
$invoices = $customer->invoices(['limit' => 5]);
```

---

## 💻 Uso Prático

### Fluxo Completo de Checkout

```php
<?php

use SierraTecnologia\Customer;
use SierraTecnologia\Charge;
use SierraTecnologia\Error;

// 1. Criar ou recuperar cliente
$customer = Customer::create([
    'email' => 'cliente@example.com',
    'name' => 'Maria Santos'
]);

try {
    // 2. Criar cobrança
    $charge = Charge::create([
        'amount' => 5000,
        'currency' => 'brl',
        'customer' => $customer->id,
        'source' => $_POST['stripeToken'],
        'description' => 'Pedido #1234'
    ]);

    if ($charge->status === 'succeeded') {
        echo "Pagamento aprovado! ID: {$charge->id}";
    }

} catch (Error\Card $e) {
    echo "Cartão recusado: " . $e->getMessage();
}
```

### Sistema de Assinaturas

```php
use SierraTecnologia\Subscription;

$subscription = Subscription::create([
    'customer' => $customer->id,
    'items' => [['plan' => 'plan_premium_monthly']],
    'trial_period_days' => 7
]);
```

---

## 🔌 Integração com Ecossistema SierraTecnologia

### Padrões de Versionamento

- **Semantic Versioning**: Major.Minor.Patch
- **API Versioning**: Via header `SierraTecnologia-Version`
- **Backward Compatibility**: Mantida em minor releases

### Testes Automatizados

```bash
# Executar suite completa de testes
./vendor/bin/phpunit

# Testes com cobertura
./vendor/bin/phpunit --coverage-html coverage/
```

### CI/CD Pipeline

A biblioteca utiliza GitHub Actions para:

- ✅ Testes em múltiplas versões PHP (7.4, 8.0, 8.1, 8.2)
- ✅ Análise estática com Psalm
- ✅ Code style check com PHPCS
- ✅ Security audit

---

## 🔧 Extensão e Customização

### Estendendo Recursos

```php
<?php

namespace App\Payment;

use SierraTecnologia\Charge as BaseCharge;

class Charge extends BaseCharge
{
    public function splitPayment(array $recipients): array
    {
        $url = $this->instanceUrl() . '/split';
        list($response, $opts) = $this->_request('post', $url, ['recipients' => $recipients]);
        return $response;
    }
}
```

---

## 📊 Exemplos Reais de Uso

### E-commerce Multi-tenant

```php
// Processar pagamento com split
$charge = \SierraTecnologia\Charge::create([
    'amount' => 10000,
    'currency' => 'brl',
    'source' => $token,
    'application_fee_amount' => 1000,
    'transfer_data' => ['destination' => $account->id]
]);
```

---

## 🤝 Guia de Contribuição

### Como Contribuir

1. **Fork** o repositório
2. **Clone** seu fork
3. **Crie branch**: `git checkout -b feature/nova-funcionalidade`
4. **Execute testes**: `./vendor/bin/phpunit`
5. **Commit**: `git commit -m "feat: adiciona nova funcionalidade"`
6. **Push**: `git push origin feature/nova-funcionalidade`
7. **Abra Pull Request**

### Executar Verificações Localmente

```bash
# Testes
./vendor/bin/phpunit

# Code style
./vendor/bin/phpcs --standard=PSR12 src/

# Análise estática
./vendor/bin/psalm

# Auto-fix
./vendor/bin/php-cs-fixer fix
```

### Política de Versionamento

- **Major (X.0.0)**: Breaking changes
- **Minor (0.X.0)**: Novas funcionalidades
- **Patch (0.0.X)**: Bug fixes

---

## 🛠️ Ferramentas de Qualidade de Código

### PHPUnit (Testes)

```bash
./vendor/bin/phpunit
./vendor/bin/phpunit --coverage-text
```

### PHPCS (Code Style)

```bash
./vendor/bin/phpcs --standard=PSR12 src/
./vendor/bin/phpcbf --standard=PSR12 src/
```

### Psalm (Análise Estática)

```bash
./vendor/bin/psalm
./vendor/bin/psalm --show-info=false
```

---

## 📚 Recursos Adicionais

### Documentação

Para referência completa da API: https://sierratecnologia.com.br/docs/api

### Licença e Suporte

- **Licença**: MIT License
- **Copyright**: © 2024 SierraTecnologia
- **Email**: contato@sierratecnologia.com.br
- **Website**: https://sierratecnologia.com.br

---

**Desenvolvido com ❤️ pela equipe SierraTecnologia**
