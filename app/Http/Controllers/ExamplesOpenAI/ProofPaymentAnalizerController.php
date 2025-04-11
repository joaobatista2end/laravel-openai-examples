<?php
namespace App\Http\Controllers\ExamplesOpenAI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenAI\Laravel\Facades\OpenAI;

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
