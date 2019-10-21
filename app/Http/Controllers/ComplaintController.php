<?php

namespace App\Http\Controllers;

use App\Complaint;
use Illuminate\Http\Request;
use DB;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $complaints = Complaint::orderBy('created_at', 'DESC')->get();
        return response()->json($complaints);
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
            'email' => 'required|string',
            'subject' => 'required|string',
            'description' => 'required|string'
        ]);

        $complaint = Complaint::create([
            'email' => $request->email,
            'subject' => $request->subject,
            'description' => $request->description
        ]);

        return response()->json($complaint, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        return response()->json($complaint);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return response()->json('Category deleted successfully');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countComplaints()
    {
        $complaints_count = DB::table('complaints')->count();
        $data = array("status" => 200, "results" => $complaints_count);

        return response()->json($data);
    }
}
