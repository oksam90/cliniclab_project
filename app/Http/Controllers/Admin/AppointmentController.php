<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Group;
use App\Models\Test;
use App\Models\Culture;
use App\Models\Branch;
use App\Models\Contract;
use App\Http\Requests\Admin\AppointmentRequest;
use Str;
use DataTables;

class AppointmentController extends Controller
{

    /**
     * assign roles
     */
    public function __construct()
    {
        $this->middleware('can:view_appointment',     ['only' => ['index', 'show','ajax']]);
        $this->middleware('can:create_appointment',   ['only' => ['create', 'store']]);
        $this->middleware('can:edit_appointment',     ['only' => ['edit', 'update']]);
        $this->middleware('can:delete_appointment',   ['only' => ['destroy']]);
        $this->middleware('can:create_group',   ['only' => ['create_tests']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.appointment.index');
    }

     /**
    * get appointments datatable
    *
    * @access public
    * @var  @Request $request
    */
    public function ajax(Request $request)
    {
        $model=Appointment::with('patient')->orderBy('id','desc');

        if($request['filter_read']!=null)
        {
            $model->where('read',$request['filter_read']);
        }

        if($request['filter_status']!=null)
        {
            $model->where('status',$request['filter_status']);
        }
        
        return DataTables::eloquent($model)

        ->editColumn('read',function($appointment){
            return view('admin.appointment._read',compact('appointment'));
        })
        ->editColumn('status',function($appointment){
            return view('admin.appointment._status',compact('appointment'));
        })
        ->addColumn('action',function($appointment){
            return view('admin.appointment._action',compact('appointment'));
        })
        ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.appointment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppointmentRequest $request)
    {
        if($request->has('patient_id'))
        {
            $patient=Patient::find($request['patient_id']);
        }
        else{
            $patient=Patient::create([
             'code'=>time(),
             'name'=>$request['name'],
             'phone'=>$request['phone'],
             'dob'=>$request['dob'],
             'address'=>$request['address'],
             'gender'=>$request['gender'],
             'email'=>$request['email'],
             'api_token'=>Str::random(32)
            ]);
        }

        $appointment=Appointment::create([
            'patient_id'=>$patient['id'],
            'lat'=>$request['lat'],
            'lng'=>$request['lng'],
            'zoom_level'=>$request['zoom_level'],
            'appointment_date'=>$request['appointment_date'],
        ]);

        if($request->has('attach'))
        {
            $attach=$request->file('attach');
            $name=time().'.'.$attach->getClientOriginalExtension();
            $attach->move('uploads/appointment',$name);
            $appointment->update(['attach'=>$name]);
        }

        session()->flash('success',__('Appointment saved successfully'));
        
        return redirect()->route('admin.appointment.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $appointment=Appointment::find($id);

        $appointment->update(['read'=>true]);

        return view('admin.appointment.show',compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $appointment=Appointment::find($id);

        $appointment->update(['read'=>true]);

        return view('admin.appointment.edit',compact('appointment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(AppointmentRequest $request, $id)
    {
        $appointment=Appointment::findOrFail($id);
        $appointment->update($request->except('_token','_method','patient_type'));

        if($request->has('attach'))
        {
            $attach=$request->file('attach');
            $name=time().'.'.$attach->getClientOriginalExtension();
            $attach->move('uploads/appointment',$name);
            $appointment=Appointment::find($id);
            $appointment->update(['attach'=>$name]);
        }

        session()->flash('success',__('Appointment updated successfully'));

        return redirect()->route('admin.appointment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $appointment=Appointment::findOrFail($id);
        $appointment->delete();

        session()->flash('success',__('Appointment deleted successfully'));
        
        return redirect()->route('admin.appointment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function create_tests($appointment_id)
    {
        $appointment=Appointment::find($appointment_id);

        $appointment->update([
            'read'=>true,
            'status'=>true,
        ]);

        $tests=Test::where('parent_id',0)->orWhere('separated',true)->get();
        $cultures=Culture::all();
        $branches=Branch::all();
        $contracts=Contract::all();

        return view('admin.groups.create',compact('appointment','tests','cultures','branches','contracts'));
    }
}
