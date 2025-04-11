# Laravel OpenAI API

Este projeto é uma API Laravel que utiliza a OpenAI para diferentes análises e processamentos.

## Configuração da OpenAI

### Obtendo a Chave da API
1. Acesse [OpenAI Platform](https://platform.openai.com/)
2. Faça login ou crie uma conta
3. Vá para [API Keys](https://platform.openai.com/api-keys)
4. Clique em "Create new secret key"
5. Copie a chave gerada (ela só será mostrada uma vez)

### Configurando o Projeto
1. Clone o repositório
2. Copie o arquivo .env.example para .env:
   ```bash
   cp .env.example .env
   ```
3. Adicione sua chave da API no arquivo .env:
   ```env
   OPENAI_API_KEY=sua-chave-aqui
   ```
4. Instale as dependências:
   ```bash
   composer install
   ```
5. Inicie o servidor:
   ```bash
   php artisan serve
   ```

### Verificando a Configuração
Para verificar se a integração está funcionando, você pode testar o endpoint de chat:
```bash
curl "http://localhost:8000/api/chat-model?search=Teste"
```

## Endpoints Disponíveis

### 1. Chat Model
Endpoint para interação com o modelo de chat GPT-4.

```http
GET /api/chat-model?search=Qual é a capital do Brasil?

{
    "content": "A capital do Brasil é Brasília."
}
```

**Detalhes do Controller (ChatModelController)**:
- Utiliza o modelo `gpt-4o-mini` para processamento de linguagem natural
- Implementa validação do parâmetro de busca obrigatório
- Integra com a API OpenAI através do facade OpenAI::chat()
- Estrutura a mensagem no formato de chat com role 'user'
- Processa a resposta e retorna apenas o conteúdo da mensagem em formato JSON
- Tratamento de erro para parâmetro de busca vazio com status 400

```php
class ChatModelController extends Controller {
    function index(Request $request) {
        $search = $request->get('search');

        if (empty($search)) {
            return response()->json([
                'error' => 'Search parameter is required'
            ], 400);
        }

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $search],
            ],
        ]);

        return response()->json($response->choices[0]->message->content, 200);
    }
}
```

### 2. Análise de Comprovante de Pagamento
Endpoint para verificar se uma imagem é um comprovante de pagamento válido.

```http
POST /api/proof-payment-analizer
Content-Type: multipart/form-data

--boundary
Content-Disposition: form-data; name="image"; filename="comprovante.jpg"
Content-Type: image/jpeg

[Conteúdo binário da imagem]
--boundary--
```

**Detalhes do Controller (ProofPaymentAnalizerController)**:
- Utiliza o modelo `gpt-4o` com suporte a análise de imagens
- Implementa validação rigorosa de upload: apenas imagens JPEG, PNG, JPG, GIF até 2MB
- Processa a imagem convertendo para base64 com prefixo data URI
- Estrutura a requisição com mensagem multimodal (texto + imagem)
- Limita a resposta a 100 tokens para garantir resposta concisa (sim/não)
- Integração direta com API Vision da OpenAI para análise de imagens

```php
class ProofPaymentAnalizerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $image = $request->file('image');
        $imageData = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($image->path()));
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Este documento é um comprovante de pagamento? Responda apenas com "sim" ou "não".',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 100,
        ]);

        return $response->choices[0]->message->content;
    }
}
```

### 3. Análise de Boleto Bancário
Endpoint para verificar se um documento PDF é um boleto bancário.

```http
POST /api/bank-slip-analizer
Content-Type: multipart/form-data

--boundary
Content-Disposition: form-data; name="document"; filename="boleto.pdf"
Content-Type: application/pdf

[Conteúdo binário do PDF]
--boundary--
```

**Detalhes do Controller (BankSlipAnalizerController)**:
- Utiliza o modelo `gpt-4o` para análise textual avançada
- Implementa validação de upload: arquivos PDF até 4MB
- Integra com biblioteca Spatie PDF to Text para extração de texto
- Implementa tratamento de erros robusto na extração do PDF
- Limita resposta a 5 tokens para garantir resposta binária (sim/não)
- Retorna resposta estruturada com flag booleana 'é_boleto' e texto original
- Normaliza a resposta para lowercase para consistência

```php
class BankSlipAnalizerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:4096',
        ]);
        $caminho = $request->file('document')->getRealPath();
        try {
            $text = (new Pdf())
                ->setPdf($caminho)
                ->text();
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Falha ao extrair texto do PDF: ' . $e->getMessage(),
            ], 500);
        }
        $resposta = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "O seguinte texto pertence a um boleto bancário? Responda com 'sim' ou 'não'.\n\n" . $text,
                ],
            ],
            'max_tokens' => 5,
        ]);

        $conteudo = trim($resposta->choices[0]->message->content ?? '');

        return response()->json([
            'é_boleto' => strtolower($conteudo) === 'sim',
            'resposta' => $conteudo,
        ]);
    }
}
```

## Requisitos
- PHP >= 8.1
- Laravel 10.x
- Chave de API da OpenAI configurada no arquivo .env

## Configuração
1. Clone o repositório
2. Execute `composer install`
3. Configure o arquivo .env com suas credenciais da OpenAI
4. Execute `php artisan serve`

## Coleção de Requisições
O projeto inclui um arquivo `api-requests.json` que contém todas as requisições pré-configuradas para testar os endpoints. Este arquivo pode ser importado tanto no Coopscotch quanto no Postman, facilitando o teste e a integração com a API.

### Como Importar
- **Coopscotch**: Abra o Coopscotch, vá em "Collections" > "Import" e selecione o arquivo `api-requests.json`
- **Postman**: Abra o Postman, clique em "Import" > "File" > "Upload Files" e selecione o arquivo `api-requests.json`
