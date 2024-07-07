<?php

namespace App\Http\Controllers\Division;

use App\Http\Controllers\Controller;
use App\Models\Division\Division;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Route Datatable
         */
        $data['datatable_route'] = route('division.dataTable');
        return view('division.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('division.create');
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Division
         */
        $divisions = Division::whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($divisions)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<a href="' . route('division.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary my-1" title="Detail"><i class="fas fa-eye"></i></a>';
                $btn_action .= '<a href="' . route('division.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning my-1 ms-1" title="Ubah"><i class="fas fa-pencil-alt"></i></a>';
                $btn_action .= '<button class="btn btn-sm btn-danger my-1 ms-1" onclick="destroy(' . $data->id . ')" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $btn_action;
            })
            ->only(['name', 'action'])
            ->rawColumns(['action'])
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
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = Division::whereNull('deleted_at')
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
                 * Create Division Record
                 */
                $division = Division::lockforUpdate()->create([
                    'name' => $request->name,
                ]);

                /**
                 * Validation Create Division Record
                 */
                if ($division) {
                    DB::commit();
                    return redirect()
                        ->route('division.index')
                        ->with(['success' => 'Successfully Add Division']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add Division'])
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
    public function show(string $id)
    {
        try {
            /**
             * Get Division from id
             */
            $division = Division::find($id);

            /**
             * Validation Division id
             */
            if (!is_null($division)) {
                $data['division'] = $division;
                return view('division.detail', $data);
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get Division from id
             */
            $division = Division::find($id);

            /**
             * Validation Division id
             */
            if (!is_null($division)) {
                $data['division'] = $division;
                return view('division.edit', $data);
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
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = Division::whereNull('deleted_at')
                ->where('name', 'like', '%' . $request->name . '%')
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {
                /**
                 * Get Division from id
                 */
                $division = Division::find($id);

                /**
                 * Validation Division id
                 */
                if (!is_null($division)) {
                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update Division Record
                     */
                    $division_update = Division::where('id', $id)->update([
                        'name' => $request->name,
                    ]);

                    /**
                     * Validation Update Division Record
                     */
                    if ($division_update) {
                        DB::commit();
                        return redirect()
                            ->route('division.index')
                            ->with(['success' => 'Successfully Update Division']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Update Division'])
                            ->withInput();
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
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
             * Destroy Division Record with Softdelete
             */
            $division_destroy = Division::where('id', $id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Softdelete Division Record
             */
            if ($division_destroy) {
                DB::commit();
                session()->flash('success', 'Division Successfully Deleted');
            } else {
                /**
                 * Failed Update Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete User');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
