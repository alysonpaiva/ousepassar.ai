<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    /**
     * Listar todos os agentes disponíveis.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $agents = Agent::with('categories', 'fields')->get();
        
        return response()->json([
            'success' => true,
            'data' => $agents
        ]);
    }
    
    /**
     * Obter detalhes de um agente específico.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $agent = Agent::with('categories', 'fields')->find($id);
        
        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agente não encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $agent
        ]);
    }
    
    /**
     * Processar um agente com dados de entrada.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(Request $request, $id)
    {
        // Encontrar o agente
        $agent = Agent::with('fields')->find($id);
        
        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agente não encontrado'
            ], 404);
        }
        
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
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $validator->errors()
            ], 422);
        }
        
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
                
                if ($isPdf) {
                    // Armazenar informações do PDF para processamento posterior
                    $hasPdfFile = true;
                    $pdfLocalPath = $localPath;
                    $pdfOriginalName = $originalName;
                    
                    // Processar como PDF - enviar diretamente para a API
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                        ])->attach(
                            'file', file_get_contents($localPath), $originalName
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
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipo de arquivo não suportado. Por favor, envie um PDF ou uma imagem (PNG, JPEG, WEBP, GIF).'
                    ], 422);
                }
                
                // Adicionar informações do arquivo para exibição
                $userInputs[$fieldLabel] = $fileInfo;
                
                // Adicionar arquivo ao array de arquivos enviados
                $uploadedFiles[] = [
                    'name' => $originalName,
                    'type' => $isPdf ? 'pdf' : 'image',
                    'path' => $path,
                    'url' => $publicUrl
                ];
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
            'model' => 'gpt-4o', // Modelo que suporta tanto texto quanto imagens e PDFs
            'input' => [
                [
                    'role' => 'user',
                    'content' => $messageContent
                ]
            ]
        ];
        
        // Chamar a API da OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
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
        
        // Retornar a resposta em formato JSON
        return response()->json([
            'success' => true,
            'data' => [
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name
                ],
                'prompt' => $prompt,
                'inputs' => $userInputs,
                'result' => $result,
                'history_id' => $history->id,
                'created_at' => $history->created_at
            ]
        ]);
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
     * Obter o histórico de interações com um agente específico.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function history($id)
    {
        $agent = Agent::find($id);
        
        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agente não encontrado'
            ], 404);
        }
        
        $history = History::where('agent_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
    
    /**
     * Obter detalhes de uma interação específica.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function historyDetail($id)
    {
        $history = History::with('agent')->find($id);
        
        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Histórico não encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
