<?php

namespace App\Http\Controllers;

use App\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $programs = Program::orderBy('created_at', 'DESC')->get();
        return response()->json($programs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:programs',
            'type' => 'required|string',
            'agency' => 'required|string'
        ]);

        $program = Program::firstOrCreate([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'agency' => $request->agency
        ]);

        return response()->json($program, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function show(Program $program)
    {
        return response()->json($program);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:programs,email,' . $id,
            'type' => 'required|string',
            'agency' => 'required|string'
        ]);

        $program->update($request->all());

        return response()->json($program);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function destroy(Program $program)
    {
        $program->delete();
        return response()->json('Program deleted successfully');
    }
}
