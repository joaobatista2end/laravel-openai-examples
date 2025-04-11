<?php

namespace App\Http\Controllers\ExamplesOpenAI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenAI\Laravel\Facades\OpenAI;
use Spatie\PdfToText\Pdf;

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
