<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Batch;
use App\Models\Harvest;
use App\Models\Novelty;

class NoveltiesController extends Controller {
    protected string $tz = 'America/Bogota';

    public function index( Request $request ) {
        $data = $this->buildData( $request );
        return view( 'admin.novelties', $data );
    }

    private function buildData( Request $request ): array {
        $tz = 'America/Bogota';

        // Fecha del filtro ( soporta 'YYYY-MM-DD' y 'DD/MM/YYYY' )
        $dateStr = $request->input( 'date' );
        if ( $dateStr && preg_match( '/^\d{2}\/\d{2}\/\d{4}$/', $dateStr ) ) {
            [ $d, $m, $y ] = explode( '/', $dateStr );
            $date = \Carbon\Carbon::createFromDate( ( int )$y, ( int )$m, ( int )$d, $tz );
        } else {
            $date = $dateStr ? \Carbon\Carbon::parse( $dateStr, $tz ) : \Carbon\Carbon::now( $tz );
        }
        $from = $date->copy()->startOfDay();
        $to   = $date->copy()->endOfDay();

        $batchId   = $request->integer( 'batch_id' ) ?: null;
        $batch     = $batchId ? \App\Models\Batch::find( $batchId ) : null;

        // 👉 Lotes disponibles exclusivamente para la fecha escogida
        $batches = \App\Models\Batch::whereBetween( 'created_at', [ $from, $to ] )
        ->orderBy( 'batchName' )
        ->get( [ 'id', 'batchName' ] );

        // 👉 Si NO hay lote elegido, no calculamos nada ( todo en 0 y tabla vacía )
        if ( !$batchId ) {
            return [
                'date'           => $date->format( 'Y-m-d' ),
                'from'           => $from,
                'to'             => $to,
                'batches'        => $batches,
                'batchId'        => null,
                'recolectados'   => 0,
                'clasificados'   => 0,
                'novedadesTotal' => 0,
                'categorias'     => [ 'AAA'=>0, 'AA'=>0, 'A'=>0, 'SUPER'=>0, 'YEMAS'=>0 ],
                'novedades'      => collect(),
            ];
        }

        // 1 ) Lotes a considerar
        if ( $batchId ) {
            $batchIds   = [ $batchId ];
            $batchNames = [ $batch?->batchName ];
        } else {
            $batchIds   = \App\Models\Batch::whereBetween( 'created_at', [ $from, $to ] )->pluck( 'id' )->all();
            $batchNames = \App\Models\Batch::whereIn( 'id', $batchIds )->pluck( 'batchName' )->all();
        }

        // === Recolectados ===
        $harvestBase = \App\Models\Harvest::query()
        ->when( !empty( $batchIds ), fn( $q ) => $q->whereIn( 'batch_id', $batchIds ) );

        // 1 ) totalEggs en la fecha
        $recolectados = ( int ) $harvestBase->clone()
        ->whereBetween( 'created_at', [ $from, $to ] )
        ->sum( 'totalEggs' );

        // 2 ) si totalEggs no está guardado, calcula bandejas * unidades
        if ( $recolectados === 0 ) {
            $recolectados = ( int ) $harvestBase->clone()
            ->whereBetween( 'created_at', [ $from, $to ] )
            ->selectRaw( 'COALESCE(SUM(trayQuantity * eggUnits), 0) as t' )
            ->value( 't' );
        }

        // 3 ) si hay lote elegido y sigue 0, repite sin fecha
        if ( $batchId && $recolectados === 0 ) {
            $recolectados = ( int ) \App\Models\Harvest::whereIn( 'batch_id', $batchIds )->sum( 'totalEggs' );

            if ( $recolectados === 0 ) {
                $recolectados = ( int ) \App\Models\Harvest::whereIn( 'batch_id', $batchIds )
                ->selectRaw( 'COALESCE(SUM(trayQuantity * eggUnits), 0) as t' )
                ->value( 't' );
            }
        }

        // 5 ) último recurso: totalBatch del( los ) lote( s )
        if ( $recolectados === 0 && !empty( $batchIds ) ) {
            $recolectados = ( int ) \Illuminate\Support\Facades\DB::table( 'batches' )
            ->whereIn( 'id', $batchIds )
            ->sum( 'totalBatch' );
        }

        // 3 ) Clasificados por categoría ( batch_details )
        $rows = \Illuminate\Support\Facades\DB::table( 'batch_details' )
        ->join( 'categories', 'batch_details.category_id', '=', 'categories.id' )
        ->selectRaw( 'UPPER(categories.categoryName) as name, COALESCE(SUM(batch_details.totalClassification),0) as total' )
        ->when( $batchIds, fn( $q ) => $q->whereIn( 'batch_details.batch_id', $batchIds ) )
        ->whereBetween( 'batch_details.created_at', [ $from, $to ] )
        ->groupBy( \Illuminate\Support\Facades\DB::raw( 'UPPER(categories.categoryName)' ) )
        ->get();

        // Normaliza a las 5 llaves fijas
        $categorias = [ 'AAA'=>0, 'AA'=>0, 'A'=>0, 'SUPER'=>0, 'YEMAS'=>0 ];
        foreach ( $rows as $r ) {
            $n = $r->name;
            $key = match ( true ) {
                $n === 'AAA' => 'AAA',
                $n === 'AA'  => 'AA',
                $n === 'A'   => 'A',
                $n === 'SUPER' || $n === 'SÚPER' => 'SUPER',
                $n === 'YEMAS' || $n === 'YEMA'  => 'YEMAS',
                default => null,
            }
            ;
            if ( $key ) {
                $categorias[ $key ] += ( int )$r->total;
            }
        }

        // Fallback sin fecha si hay lote seleccionado y todo quedó en 0
        if ( $batchId && array_sum( $categorias ) === 0 ) {
            $rows = \Illuminate\Support\Facades\DB::table( 'batch_details' )
            ->join( 'categories', 'batch_details.category_id', '=', 'categories.id' )
            ->selectRaw( 'UPPER(categories.categoryName) as name, COALESCE(SUM(batch_details.totalClassification),0) as total' )
            ->whereIn( 'batch_details.batch_id', $batchIds )
            ->groupBy( \Illuminate\Support\Facades\DB::raw( 'UPPER(categories.categoryName)' ) )
            ->get();

            $categorias = [ 'AAA'=>0, 'AA'=>0, 'A'=>0, 'SUPER'=>0, 'YEMAS'=>0 ];
            foreach ( $rows as $r ) {
                $n = $r->name;
                $key = match ( true ) {
                    $n === 'AAA' => 'AAA',
                    $n === 'AA'  => 'AA',
                    $n === 'A'   => 'A',
                    $n === 'SUPER' || $n === 'SÚPER' => 'SUPER',
                    $n === 'YEMAS' || $n === 'YEMA'  => 'YEMAS',
                    default => null,
                }
                ;
                if ( $key ) {
                    $categorias[ $key ] += ( int )$r->total;
                }
            }
        }

        $clasificados = array_sum( $categorias );

        // 4 ) fallback coherente con lo visto en pantalla
        if ( $recolectados === 0 ) {
            $recolectados = ( int ) ( $clasificados + $novedadesTotal );
        }

        // 4 ) Novedades: siempre por fecha, si hay lote elegido, por batch_code también
        $novQuery = \App\Models\Novelty::query()
        ->whereBetween( 'created_at', [ $from, $to ] );

        if ( !empty( $batchNames ) ) {
            $novQuery->whereIn( 'batch_code', $batchNames );
        }

        $novedadesTotal = ( int ) $novQuery->sum( 'quantity' );
        $novedades = $novQuery->orderByDesc( 'created_at' )
        ->get( [ 'created_at', 'batch_code', 'quantity', 'novelty', 'user_name' ] );

        // 5 ) Para el select
        $batches = \App\Models\Batch::orderBy( 'batchName' )->get( [ 'id', 'batchName' ] );

        return [
            'date'           => $date->format( 'Y-m-d' ),
            'from'           => $from,
            'to'             => $to,
            'batches'        => $batches,
            'batchId'        => $batchId,
            'recolectados'   => ( int ) $recolectados,
            'clasificados'   => ( int ) $clasificados,
            'novedadesTotal' => ( int ) $novedadesTotal,
            'categorias'     => $categorias,
            'novedades'      => $novedades,
        ];
    }

    public function batchesByDate( Request $request ) {
        $request->validate( [ 'date' => [ 'required', 'string' ] ] );

        $tz = 'America/Bogota';
        $dateStr = $request->string( 'date' )->toString();

        if ( preg_match( '/^\d{2}\/\d{2}\/\d{4}$/', $dateStr ) ) {
            [ $d, $m, $y ] = explode( '/', $dateStr );
            $date = \Carbon\Carbon::createFromDate( ( int )$y, ( int )$m, ( int )$d, $tz );
        } else {
            $date = \Carbon\Carbon::parse( $dateStr, $tz );
        }

        $from = $date->copy()->startOfDay();
        $to   = $date->copy()->endOfDay();

        $batches = \App\Models\Batch::whereBetween( 'created_at', [ $from, $to ] )
        ->orderBy( 'batchName' )
        ->get( [ 'id', 'batchName' ] );

        return response()->json( [ 'data' => $batches ] );
    }

}
