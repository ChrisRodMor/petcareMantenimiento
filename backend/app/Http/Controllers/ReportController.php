<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports=Report::all();
        return response()->json(['data' => $reports], 200);
    }
    
    public function statusSummary(Request $request)
    {
        // 1. Cálculo de totales
        $attended = Report::where('status', 'Terminado')->count();
        $pending  = Report::whereIn('status', ['Revisando', 'Avanzando'])->count();
        $total    = $attended + $pending;

        // 2. Preparar labels y data para front
        $labels = ['Atendidos', 'Pendientes'];
        $data   = [$attended, $pending];

        return response()->json([
            'total_reports' => $total,
            'labels'        => $labels,
            'data'          => $data,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        
        // Obtener el tipo de reporte
        $typeReport = $report->type_report;

        // Definir las relaciones a cargar según el tipo de reporte
        $relations = [];
        if ($typeReport === 'ADOPCION') {
            $relations[] = 'adoptionReport';
        } elseif ($typeReport === 'MASCOTA_PERDIDA') {
            $relations[] = 'lostPetReport';
        } elseif ($typeReport === 'MALTRATO') {
            $relations[] = 'abuseReport';
        }

        // Cargar las relaciones correspondientes usando with()
        $report = Report::with($relations)->find($report->id);

        // Retornar una respuesta JSON con los datos del reporte y su información adicional
        return response()->json(['data' => $report], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:Revisando,Avanzando,Terminado',
        ], [
            // Mensajes personalizados
            'status.required' => 'El campo :attribute es obligatorio.',
            'status.in' => 'El campo :attribute debe ser uno de los siguientes: Revisando, Avanzando o Terminado.',
        ], [
            // Traducciones de atributos
            'status' => 'estado del reporte',
        ]);
        $report->update([
            'status' => $request->input('status'), // Asignar el nuevo valor de 'status' desde el request
        ]);

        // Retornar una respuesta JSON indicando que los datos han sido actualizados correctamente
        return response()->json(['message' => 'El status del reporte ha sido actualizado correctamente', 'data' => $report], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }

    public function getReports(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Obtener todos los reportes del usuario autenticado
        $reports = $user->reports;

        // Retornar una respuesta JSON con los reportes
        return response()->json(['data' => $reports], 200);
    }
}
