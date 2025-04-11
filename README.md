# Laravel OpenAI API

Este projeto é uma API Laravel que utiliza a OpenAI para diferentes análises e processamentos.

## Endpoints Disponíveis

### 1. Chat Model
Endpoint para interação com o modelo de chat GPT-4.

```http
GET /api/chat-model

Query Parameters:
  search: string (required) - O texto para interagir com o modelo

### Exemplo de Requisição
GET /api/chat-model?search=Qual é a capital do Brasil?

### Resposta
{
    "content": "A capital do Brasil é Brasília."
}
```

**Detalhes do Controller (ChatModelController)**:
- Utiliza o modelo `gpt-4o-mini`
- Valida se o parâmetro de busca foi fornecido
- Retorna a resposta do modelo em formato JSON

### 2. Análise de Comprovante de Pagamento
Endpoint para verificar se uma imagem é um comprovante de pagamento válido.

```http
POST /api/proof-payment-analizer
Content-Type: multipart/form-data

Body:
  image: file (required) - Imagem do comprovante (jpeg, png, jpg, gif, max: 2MB)

### Exemplo de Requisição
POST /api/proof-payment-analizer
Content-Type: multipart/form-data

--boundary
Content-Disposition: form-data; name="image"; filename="comprovante.jpg"
Content-Type: image/jpeg

[Conteúdo binário da imagem]
--boundary--

### Resposta
"sim" ou "não"
```

**Detalhes do Controller (ProofPaymentAnalizerController)**:
- Utiliza o modelo `gpt-4o`
- Aceita imagens nos formatos JPEG, PNG, JPG, GIF
- Converte a imagem para base64
- Analisa a imagem e responde se é um comprovante de pagamento

### 3. Análise de Boleto Bancário
Endpoint para verificar se um documento PDF é um boleto bancário.

```http
POST /api/bank-slip-analizer
Content-Type: multipart/form-data

Body:
  document: file (required) - Arquivo PDF do boleto (max: 4MB)

### Exemplo de Requisição
POST /api/bank-slip-analizer
Content-Type: multipart/form-data

--boundary
Content-Disposition: form-data; name="document"; filename="boleto.pdf"
Content-Type: application/pdf

[Conteúdo binário do PDF]
--boundary--

### Resposta
{
    "resposta": "sim" ou "não"
}
```

**Detalhes do Controller (BankSlipAnalizerController)**:
- Utiliza o modelo `gpt-4o`
- Aceita apenas arquivos PDF
- Extrai o texto do PDF usando a biblioteca Spatie PDF to Text
- Analisa o conteúdo e determina se é um boleto bancário

## Requisitos
- PHP >= 8.1
- Laravel 10.x
- Chave de API da OpenAI configurada no arquivo .env

## Configuração
1. Clone o repositório
2. Execute `composer install`
3. Configure o arquivo .env com suas credenciais da OpenAI
4. Execute `php artisan serve`
