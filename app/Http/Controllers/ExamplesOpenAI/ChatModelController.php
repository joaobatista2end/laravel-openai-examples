<?php
namespace App\Http\Controllers\ExamplesOpenAI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Completions\CreateResponse;

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
