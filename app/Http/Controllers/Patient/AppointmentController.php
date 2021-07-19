<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use App\Http\Requests\Patient\AppointmentRequest;
use Str;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patient=Patient::findOrFail(auth()->guard('patient')->user()['id']);
        return view('patient.appointment.index',compact('patient'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppointmentRequest $request)
    {
        if($request->patient_type==2)
        {
            $patient=Patient::find(auth()->guard('patient')->user()['id']);
        }
        else{
            $patient=Patient::create([
                'code'=>time(),
                'name'=>$request['name'],
                'phone'=>$request['phone'],
                'dob'=>$request['dob'],
                'address'=>$request['address'],
                'gender'=>$request['gender'],
                'email'=>$request['email']
            ]);
        }

        //create patient appointment
        $appointment=Appointment::create([
            'patient_id'=>$patient['id'],
            'lat'=>$request['lat'],
            'lng'=>$request['lng'],
            'zoom_level'=>$request['zoom_level'],
            'appointment_date'=>$request['appointment_date'],
        ]);
        
        //add attach to appointment request
        if($request->has('attach'))
        {
            $attach=$request->file('attach');
            $name=time().'.'.$attach->getClientOriginalExtension();
            $attach->move('uploads/appointment',$name);
            $appointment->update(['attach'=>$name]);
        }

        session()->flash('success',__('Your appointment request sent successfully , please be patient till our representative contact you'));

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
