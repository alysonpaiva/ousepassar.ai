<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    /**
     * Gerar PDF a partir do resultado de uma interação.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $historyId
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(Request $request, $historyId = null)
    {
        try {
            $agent = null;
            $result = null;
            $userInputs = [];
            $prompt = null;
            $date = null;

            // Se o historyId for fornecido, buscar do banco de dados
            if ($historyId) {
                $history = History::with('agent')->findOrFail($historyId);
                $agent = $history->agent;
                $result = $history->result ?? 'N/A';
                $userInputs = $history->input_data ?? [];
                $prompt = $history->prompt ?? 'N/A';
                $date = $history->created_at ? $history->created_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s');
            } else {
                // Caso contrário, usar os dados da requisição (para geração em tempo real)
                $agentId = $request->input('agent_id');
                if ($agentId) {
                    $agent = Agent::find($agentId);
                }
                $result = $request->input('result', 'N/A');
                $userInputsJson = $request->input('user_inputs');
                $userInputs = $userInputsJson ? json_decode($userInputsJson, true) : [];
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('Erro ao decodificar user_inputs para PDF: ' . json_last_error_msg());
                    $userInputs = []; // Resetar em caso de erro
                }
                $prompt = $request->input('prompt', 'N/A');
                $date = now()->format('d/m/Y H:i:s');
            }

            // Garantir que $agent não seja nulo
            if (!$agent) {
                Log::error('Agente não encontrado para geração de PDF.');
                // Você pode retornar um erro aqui ou usar um agente padrão/placeholder
                // Por enquanto, vamos criar um objeto genérico para evitar erros na view
                $agent = new \stdClass();
                $agent->name = 'Agente Desconhecido';
                $agent->id = 0;
            }

            // Preparar os dados para o PDF, garantindo que todos os índices existam
            $data = [
                'agent' => $agent,
                'result' => $result ?? 'N/A',
                'userInputs' => is_array($userInputs) ? $userInputs : [],
                'prompt' => $prompt ?? 'N/A',
                'date' => $date ?? now()->format('d/m/Y H:i:s'),
                'title' => 'Resultado - ' . ($agent->name ?? 'Agente Desconhecido')
            ];

            // Gerar o PDF
            $pdf = PDF::loadView('pdf.result', $data);

            // Definir opções do PDF
            $pdf->setPaper('a4');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false, // Desabilitar carregamento de recursos remotos
                'defaultFont' => 'sans-serif',
                // Adicionar logs de depuração do DomPDF (opcional)
                // 'logOutputFile' => storage_path('logs/dompdf.log'),
                // 'debugPng' => true,
                // 'debugKeepTemp' => true,
                // 'debugCss' => true,
                // 'debugLayout' => true,
                // 'debugLayoutLines' => true,
                // 'debugLayoutBlocks' => true,
                // 'debugLayoutInline' => true,
                // 'debugLayoutPaddingBox' => true
            ]);

            // Retornar o PDF para download
            return $pdf->download('resultado_' . ($agent->id ?? 0) . '' . date('YmdHis') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            // Retornar uma resposta de erro amigável
            return response('Erro ao gerar o PDF. Por favor, tente novamente ou contate o suporte.', 500);
        }
    }
}
