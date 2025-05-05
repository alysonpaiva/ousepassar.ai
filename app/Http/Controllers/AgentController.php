<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Category;
use App\Models\Field;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Parsedown;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = Agent::with('categories')->get();
        $stylesheet = '';
        return view('agents.index', compact('agents', 'stylesheet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $stylesheet = '';
        return view('agents.create', compact('categories', 'stylesheet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'prompt' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'temp_fields' => 'nullable|array',
        ]);

        $agent = Agent::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'prompt' => $request->prompt,
        ]);

        $agent->categories()->attach($request->categories);

        // Processar campos temporários
        if ($request->has('temp_fields')) {
            foreach ($request->temp_fields as $tempField) {
                $fieldData = json_decode($tempField, true);

                $field = new Field([
                    'name' => $fieldData['name'],
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'options' => $fieldData['type'] === 'select' ? $fieldData['options'] : null,
                    'required' => $fieldData['required'],
                    'order' => Field::where('agent_id', $agent->id)->count(),
                ]);

                $agent->fields()->save($field);
            }
        }

        return redirect()->route('agents.edit', $agent)
            ->with('success', 'Agente criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent)
    {
        $agent->load('categories', 'fields');
        $stylesheet = '';

        // Carregar histórico recente para este agente
        $recentHistory = History::where('agent_id', $agent->id)
            ->where("user_id", Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(-1)
            ->get();

        return view('agents.show', compact('agent', 'stylesheet', 'recentHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent)
    {
        $categories = Category::all();
        $agent->load('categories', 'fields');
        $stylesheet = '';
        return view('agents.edit', compact('agent', 'categories', 'stylesheet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agent $agent)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'prompt' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $agent->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'prompt' => $request->prompt,
        ]);

        $agent->categories()->sync($request->categories);

        return redirect()->route('agents.edit', $agent)
            ->with('success', 'Agente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        $agent->delete();
        return redirect()->route('agents.index')
            ->with('success', 'Agente excluído com sucesso!');
    }

    /**
     * Process the agent with user input.
     */
    public function process(Request $request, Agent $agent)
    {
        $agent->load('fields');

        // Validar os campos dinâmicos
        $rules = [];
        $fieldValues = [];
        $uploadedFiles = [];

        foreach ($agent->fields as $field) {
            $rule = $field->required ? 'required' : 'nullable';

            if ($field->type === 'upload') {
                // Aceitar PDF e imagens (PNG, JPEG, WEBP, GIF)
                $rule .= '|file|mimes:pdf,png,jpg,jpeg,webp,gif|max:20480'; // 20MB max
            }

            $rules["field_{$field->id}"] = $rule;
        }

        $validated = $request->validate($rules);

        // Processar os valores dos campos e preparar o conteúdo da mensagem
        $messageContent = [];
        $userInputs = [];
        $hasPdfFile = false;
        $pdfLocalPath = null;
        $pdfOriginalName = null;

        foreach ($agent->fields as $field) {
            $fieldName = "field_{$field->id}";
            $fieldLabel = $field->label;

            if ($field->type === 'upload' && $request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $mimeType = $file->getMimeType();
                $originalName = $file->getClientOriginalName();

                // Salvar o arquivo localmente primeiro
                $path = $file->store('uploads', 'public');
                $localPath = Storage::disk('public')->path($path);
                $publicUrl = asset('storage/' . $path);

                // Verificar se é um PDF ou uma imagem
                $isPdf = $mimeType === 'application/pdf';
                $isImage = strpos($mimeType, 'image/') === 0;

                // Preparar informações do arquivo para exibição
                $fileInfo = [
                    'type' => $isPdf ? 'pdf' : 'image',
                    'name' => $originalName,
                    'url' => $publicUrl,
                    'mime' => $mimeType
                ];

                // Adicionar arquivo ao array de arquivos enviados
                $uploadedFiles[] = [
                    'name' => $originalName,
                    'type' => $isPdf ? 'pdf' : 'image',
                    'path' => $path,
                    'url' => $publicUrl
                ];

                if ($isPdf) {
                    // Armazenar informações do PDF para processamento posterior
                    $hasPdfFile = true;
                    $pdfLocalPath = $localPath;
                    $pdfOriginalName = $originalName;

                    // Processar como PDF - enviar diretamente para a API
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . config('settings.OPENAI_API_KEY'),
                        ])->attach(
                            'file',
                            file_get_contents($localPath),
                            $originalName
                        )->post('https://api.openai.com/v1/files', [
                            'purpose' => 'user_data',
                        ]);

                        if ($response->successful()) {
                            $fileData = $response->json();
                            $fileId = $fileData['id'];

                            // Adicionar o arquivo ao conteúdo da mensagem como input_file
                            $messageContent[] = [
                                'type' => 'input_file',
                                'file_id' => $fileId
                            ];

                            // Armazenar o ID do arquivo da OpenAI no campo
                            $fieldValues[$field->name] = $fileId;

                            // Adicionar informações do arquivo para exibição
                            $fileInfo['openai_id'] = $fileId;
                        } else {
                            // Em caso de erro, usar a URL local
                            $fieldValues[$field->name] = $publicUrl;

                            // Verificar se o erro é relacionado ao tamanho do arquivo
                            $errorBody = $response->body();
                            if (strpos($errorBody, 'token count') !== false || strpos($errorBody, 'context_length_exceeded') !== false) {
                                // Erro de limite de tokens - usar abordagem alternativa
                                Log::info("Erro de limite de tokens ao processar PDF: {$originalName}. Usando abordagem alternativa.");

                                // Adicionar uma mensagem explicativa ao prompt
                                $pdfMessage = "O PDF '{$originalName}' é muito grande para ser processado diretamente. " .
                                    "Por favor, considere dividir o PDF em partes menores ou extrair as seções mais relevantes.";

                                // Adicionar a mensagem ao conteúdo
                                $messageContent[] = [
                                    'type' => 'input_text',
                                    'text' => $pdfMessage
                                ];

                                // Adicionar informações do arquivo para exibição
                                $fileInfo['size_warning'] = "PDF muito grande para processamento direto";
                            }
                        }
                    } catch (\Exception $e) {
                        // Em caso de exceção, usar a URL local
                        Log::error("Erro ao processar PDF: " . $e->getMessage());
                        $fieldValues[$field->name] = $publicUrl;
                    }
                } elseif ($isImage) {
                    // Processar como imagem
                    // Para imagens, codificamos em base64 e enviamos diretamente
                    $base64Image = base64_encode(file_get_contents($localPath));
                    $imageType = $mimeType;

                    // Adicionar a imagem ao conteúdo da mensagem como input_image
                    $messageContent[] = [
                        'type' => 'input_image',
                        'image_url' => "data:{$imageType};base64,{$base64Image}",
                        'detail' => 'high' // Usar alta resolução para melhor análise
                    ];

                    // Armazenar a URL local da imagem no campo
                    $fieldValues[$field->name] = $publicUrl;
                } else {
                    // Tipo de arquivo não suportado
                    return back()->withErrors([
                        $fieldName => 'Tipo de arquivo não suportado. Por favor, envie um PDF ou uma imagem (PNG, JPEG, WEBP, GIF).'
                    ]);
                }

                // Adicionar informações do arquivo para exibição
                $userInputs[$fieldLabel] = $fileInfo;
            } else {
                $fieldValue = $request->input($fieldName);
                $fieldValues[$field->name] = $fieldValue;

                // Adicionar valor do campo para exibição
                $userInputs[$fieldLabel] = [
                    'type' => 'text',
                    'value' => $fieldValue
                ];
            }
        }

        // Substituir os placeholders no prompt
        $prompt = $agent->prompt;

        foreach ($fieldValues as $name => $value) {
            $prompt = str_replace("[$name]", $value, $prompt);
        }

        // Adicionar o texto do prompt ao conteúdo da mensagem
        $messageContent[] = [
            'type' => 'input_text',
            'text' => $prompt
        ];

        // Preparar a chamada para a API da OpenAI usando o endpoint /v1/responses
        $requestData = [
            'model' => 'gpt-4o',
            'input' => [
                [
                    'role' => 'system',
                    'content' => config('settings.OPENAI_ASSISTENTE')
                ],
                [
                    'role' => 'user',
                    'content' => $messageContent
                ]
            ],
            'temperature' => 0.7
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('settings.OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', $requestData);


        if ($response->successful()) {
            // Verificar a estrutura da resposta e extrair o texto de saída
            $responseData = $response->json();

            // Registrar a resposta completa para debug
            Log::info('OpenAI Response:', $responseData);

            // Extrair o texto com base na estrutura exata da resposta
            $result = $this->extractResponseText($responseData);

            if (!$result) {
                $result = 'Não foi possível extrair o texto da resposta. Resposta completa: ' . json_encode($responseData);
            }

            $parsedown = new Parsedown();
            $parsedown->setSafeMode(true);
            $result = $parsedown->text($result);
        } else {
            $errorBody = $response->body();

            // Verificar se o erro é relacionado ao tamanho do arquivo
            if ($hasPdfFile && (strpos($errorBody, 'token count') !== false || strpos($errorBody, 'context_length_exceeded') !== false)) {
                // Erro de limite de tokens - usar abordagem alternativa
                Log::info("Erro de limite de tokens ao processar PDF. Usando abordagem alternativa.");

                // Criar uma mensagem explicativa
                $result = "O PDF fornecido é muito grande para ser processado diretamente pela API. " .
                    "Por favor, considere uma das seguintes opções:\n\n" .
                    "1. Dividir o PDF em partes menores\n" .
                    "2. Extrair apenas as seções mais relevantes\n" .
                    "3. Resumir o conteúdo do PDF antes de enviá-lo\n\n" .
                    "Erro original: " . $errorBody;
            } else {
                $result = 'Erro ao processar a solicitação: ' . $errorBody;
            }
        }

        // Salvar o histórico da interação
        $history = new History([
            'agent_id' => $agent->id,
            'user_id' => auth()->id(), // Será null se não estiver autenticado
            'input_data' => $userInputs,
            'prompt' => $prompt,
            'result' => $result,
            'files' => $uploadedFiles,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        $history->save();

        // Se for uma requisição AJAX, retornar os dados para exibição na mesma página
        if ($request->ajax()) {
            return response()->json([
                'result' => $result,
                'userInputs' => $userInputs,
                'prompt' => $prompt,
                'history_id' => $history->id
            ]);
        }

        // Caso contrário, retornar a view completa (para compatibilidade)
        $stylesheet = '';
        return view('agents.result', compact('agent', 'result', 'fieldValues', 'userInputs', 'stylesheet', 'history'));
    }

    /**
     * Extrai o texto da resposta da API da OpenAI.
     *
     * @param array $responseData Dados da resposta da API
     * @return string|null Texto extraído ou null se não encontrado
     */
    private function extractResponseText($responseData)
    {
        // Extrair o texto com base na estrutura da resposta
        if (isset($responseData['output']) && is_array($responseData['output']) && !empty($responseData['output'])) {
            $outputItem = $responseData['output'][0];
            if (isset($outputItem['content']) && is_array($outputItem['content']) && !empty($outputItem['content'])) {
                foreach ($outputItem['content'] as $content) {
                    if (isset($content['type']) && $content['type'] === 'output_text' && isset($content['text'])) {
                        return $content['text'];
                    }
                }
            }
        }

        // Fallbacks para outros formatos possíveis
        if (isset($responseData['output_text'])) {
            return $responseData['output_text'];
        } elseif (isset($responseData['choices']) && isset($responseData['choices'][0]['message']['content'])) {
            return $responseData['choices'][0]['message']['content'];
        }

        return null;
    }

    /**
     * Exibir o histórico de interações com um agente.
     *
     * @param Agent $agent
     * @return \Illuminate\View\View
     */
    public function history(Agent $agent)
    {
        $history = History::where("agent_id", $agent->id)
            ->where("user_id", Auth::id()) // Filtrar pelo usuário logado
            ->latest()
            ->paginate(15);
        return view("agents.history", compact("agent", "history"));
    }

    public function historyDetail(History $history)
    {
        if ($history->user_id !== Auth::id()) {
            abort(403, "Acesso não autorizado.");
        }
        $history->load("agent");
        $agent = $history->agent; // <-- Extrair o agente da relação
        return view("agents.history_detail", compact("history", "agent")); // <-- Passar $agent para a view
    }

    /**
     * Novo método para buscar dados do histórico via AJAX.
     */
    public function getHistoryData(History $history)
    {
        // Verificar se o histórico pertence ao usuário logado
        if ($history->user_id !== Auth::id()) {
            return response()->json(["success" => false, "error" => "Acesso não autorizado."], 403);
        }

        // Retornar os dados necessários em formato JSON
        return response()->json([
            "success" => true,
            "result" => $history->result,
            "userInputs" => $history->input_data,
            "prompt" => $history->prompt,
            "historyId" => $history->id, // Para o botão PDF
        ]);
    }
}
