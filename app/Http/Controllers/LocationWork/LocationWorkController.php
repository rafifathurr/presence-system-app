<?php

namespace App\Http\Controllers\LocationWork;

use App\Http\Controllers\Controller;
use App\Models\LocationWork\LocationWork;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LocationWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Route Datatable
         */
        $data['datatable_route'] = route('location-work.dataTable');
        return view('location_work.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('location_work.create');
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Location Work
         */
        $locations = LocationWork::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($locations)
            ->addIndexColumn()
            ->addColumn('latlong', function ($data) {
                /**
                 * Return Lat Long Direct to Gmaps
                 */
                return '<a target="_blank" href="https://www.google.com/maps/@' .
                    $data->latitude .
                    ',' .
                    $data->longitude .
                    ',15z?entry=ttu">
                                    ' .
                    $data->latitude .
                    ',' .
                    $data->longitude .
                    '<i class="fas fa-external-link-alt ms-1"></i></a>';
            })
            ->addColumn('radius', function ($data) {
                return $data->radius . ' Meter';
            })
            ->addColumn('status', function ($data) {
                /**
                 * Condition Status
                 */
                if (date('Y-m-d') >= $data->date_start && date('Y-m-d') <= $data->date_finish) {
                    return '<span class="badge badge-success p-1 px-2 rounded-pill">On Progress</span>';
                } else {
                    if (date('Y-m-d') <= $data->date_start && date('Y-m-d') <= $data->date_finish) {
                        return '<span class="badge badge-warning p-1 px-2 rounded-pill">On Plan</span>';
                    } else {
                        return '<span class="badge badge-danger p-1 px-2 rounded-pill">Finish</span>';
                    }
                }
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<a href="' . route('location-work.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary my-1" title="Detail"><i class="fas fa-eye"></i></a>';
                $btn_action .= '<a href="' . route('location-work.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning my-1 ms-1" title="Ubah"><i class="fas fa-pencil-alt"></i></a>';
                $btn_action .= '<button class="btn btn-sm btn-danger my-1 ms-1" onclick="destroy(' . $data->id . ')" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $btn_action;
            })
            ->only(['name', 'latlong', 'radius', 'address', 'action'])
            ->rawColumns(['latlong', 'action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'name' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'radius' => 'required',
                'address' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = LocationWork::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('name', 'like', '%' . $request->name . '%')
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create Location Work Record
                 */
                $location_work = LocationWork::lockforUpdate()->create([
                    'name' => $request->name,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'radius' => $request->radius,
                    'address' => $request->address,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Store Location Work Record
                 */
                if ($location_work) {
                    DB::commit();
                    return redirect()
                        ->route('location-work.index')
                        ->with(['success' => 'Successfully Add Location Work']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add Location Work'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Name Already Exist'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            /**
             * Check Request Type
             */
            if ($request->ajax()) {
                /**
                 * Get Location Work from id
                 */
                $location_work = LocationWork::find($id);

                /**
                 * Validation Location Work id
                 */
                if (!is_null($location_work)) {
                    return response()->json($location_work, 200);
                } else {
                    return response()->json(null, 404);
                }
            } else {
                /**
                 * Get Location Work from id
                 */
                $location_work = LocationWork::find($id);

                /**
                 * Validation Location Work id
                 */
                if (!is_null($location_work)) {
                    $data['location_work'] = $location_work;
                    return view('location_work.detail', $data);
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            }
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json($e->getMessage(), 400);
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => $e->getMessage()]);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get LocationWork from id
             */
            $location_work = LocationWork::find($id);

            /**
             * Validation Warrant id
             */
            if (!is_null($location_work)) {
                $data['location_work'] = $location_work;
                return view('location_work.edit', $data);
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'name' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'radius' => 'required',
                'address' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = LocationWork::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('id', '!=', $id)
                ->where('name', 'like', '%' . $request->name . '%')
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Location Work Record
                 */
                $location_work_update = LocationWork::where('id', $id)->update([
                    'name' => $request->name,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'radius' => $request->radius,
                    'address' => $request->address,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Update Location Work Record
                 */
                if ($location_work_update) {
                    DB::commit();
                    return redirect()
                        ->route('location-work.index')
                        ->with(['success' => 'Successfully Update Location Work']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Update Location Work'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Name Already Exist'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            /**
             * Destroy Location Work Record with Softdelete
             */
            $location_work_destroy = LocationWork::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Softdelete Location Work Record
             */
            if ($location_work_destroy) {
                DB::commit();
                session()->flash('success', 'Location Work Successfully Deleted');
            } else {
                /**
                 * Failed Update Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Location Work');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
