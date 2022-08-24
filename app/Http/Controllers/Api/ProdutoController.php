<?php

namespace App\Http\Controllers\Api;

use App\Models\Produto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Entrada;
use App\Models\Saida;
use GuzzleHttp\Handler\Proxy;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = empty($request->page)? 15:$request->page;
        $data = Produto::with('saidas');
            if($request->has('nome')){
                $data->where('nome', 'LIKE',"%{$request->nome}%");
                //buscando pelo nome
            }
                dd($data->toSql());
                return response()->json($data->paginate($page));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'nome' => 'required',
            'descricao' => 'nullable|string',
            'dataEntrada' => 'nullable|date',
            'dataSaida' => 'nullable|date',
            'tipo' => 'nullable|string'
        ]);
        $produto = Produto::create($validated);
        return response()->json($produto, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

            $produto = Produto::whereId($id)->first();
            return response()->json($produto);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produto $produto)
    {
        $validated = $request->validate([
            'nome' => 'nullable|string',
            'descricao' => 'nullable|string',
            'dataEntrada' => 'nullable|date',
            'dataSaida' => 'nullable|date',
            'tipo' => 'nullable|string'
        ]);
        $produto->update($validated);

        if($request->exists('dataSaida')){
            $produto->saidas()->create($validated);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produto $produto)
    {
        $produto->saidas()->delete();
        $produto->delete();
        return response()->noContent();
    }
}
