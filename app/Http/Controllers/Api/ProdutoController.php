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
        // dd($request->all());
        if(!$request->has('nome')){
            $data = Produto::with('saidas')->paginate(15);
            return response()->json($data);

            //buscando pelo nome
        }
            $produto = Produto::where('nome', 'LIKE',"%{$request->nome}%")->with('saidas')->paginate(2);
            return response()->json($produto);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       //dd($request->all());
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
        // dd($request->all());
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
