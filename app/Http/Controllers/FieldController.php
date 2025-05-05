<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Agent $agent)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,select,upload,date',
            'options' => 'nullable|array|required_if:type,select',
            'options.*' => 'nullable|string|max:255',
            'required' => 'nullable|boolean',
        ]);

        $field = new Field([
            'name' => $request->name,
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->type === 'select' ? $request->options : null,
            'required' => $request->has('required'),
            'order' => Field::where('agent_id', $agent->id)->count(),
        ]);

        $agent->fields()->save($field);

        return redirect()->route('agents.edit', $agent)
            ->with('success', 'Campo adicionado com sucesso!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Field $field)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,select,upload,date',
            'options' => 'nullable|array|required_if:type,select',
            'options.*' => 'nullable|string|max:255',
            'required' => 'nullable|boolean',
        ]);

        $field->update([
            'name' => $request->name,
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->type === 'select' ? $request->options : null,
            'required' => $request->has('required'),
        ]);

        return redirect()->route('agents.edit', $field->agent)
            ->with('success', 'Campo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        $agent = $field->agent;
        $field->delete();

        // Reordenar os campos restantes
        $agent->fields()->orderBy('order')->get()->each(function ($field, $index) {
            $field->update(['order' => $index]);
        });

        return redirect()->route('agents.edit', $agent)
            ->with('success', 'Campo excluído com sucesso!');
    }

    /**
     * Reorder fields.
     */
    public function reorder(Request $request, Agent $agent)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:fields,id',
            'fields.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->fields as $fieldData) {
            $field = Field::find($fieldData['id']);
            if ($field && $field->agent_id === $agent->id) {
                $field->update(['order' => $fieldData['order']]);
            }
        }

        return response()->json(['success' => true]);
    }
}
