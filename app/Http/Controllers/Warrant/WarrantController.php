<?php

namespace App\Http\Controllers\Warrant;

use App\Exports\PresenceWarrantExport;
use App\Http\Controllers\Controller;
use App\Models\LocationWork\LocationWork;
use App\Models\User;
use App\Models\Warrant\Warrant;
use App\Models\Warrant\WarrantUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class WarrantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Route Datatable
         */
        $data['datatable_route'] = route('warrant.dataTable');
        return view('warrant.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * Get All Users
         */
        $data['users'] = User::whereNull('deleted_at')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'staff');
            })
            ->get();

        /**
         * Get All Location Work
         */
        $data['location_works'] = LocationWork::whereNull('deleted_by')->whereNull('deleted_at')->get();
        return view('warrant.create', $data);
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        if (User::find(Auth::user()->id)->hasRole('admin')) {
            /**
             * Get All Warrant
             */
            $warrants = Warrant::with(['presence'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->get();
        } else {
            /**
             * Get All Warrant by User
             */
            $warrants = Warrant::with(['presence'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->whereHas('warrantUser', function ($query) {
                    $query->where('user_id', Auth::user()->id);
                })
                ->get();
        }

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($warrants)
            ->addIndexColumn()
            ->addColumn('duration', function ($data) {
                /**
                 * Return Format Date & Time Duration
                 */
                return date('d F Y', strtotime($data->date_start)) . ' - ' . date('d F Y', strtotime($data->date_finish));
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
                $btn_action = '<a href="' . route('warrant.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary my-1" title="Detail"><i class="fas fa-eye"></i></a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole('admin')) {
                    /**
                     * Allow Edit or Delete when presence empty
                     */
                    dd($data->presence->toArray());
                    if (empty($data->presence)) {
                        $btn_action .= '<a href="' . route('warrant.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning my-1 ms-1" title="Ubah"><i class="fas fa-pencil-alt"></i></a>';
                        $btn_action .= '<button class="btn btn-sm btn-danger my-1 ms-1" onclick="destroy(' . $data->id . ')" title="Hapus"><i class="fas fa-trash"></i></button>';
                    }
                }
                return $btn_action;
            })
            ->only(['name', 'duration', 'status', 'action'])
            ->rawColumns(['status', 'action'])
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
                'date_start' => 'required|date',
                'date_finish' => 'required|date',
                'location_work' => 'required',
                'attachment' => 'required',
                'warrant_user' => 'required',
            ]);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Create Warrant Record
             */
            $warrant = Warrant::lockforUpdate()->create([
                'name' => $request->name,
                'date_start' => $request->date_start,
                'date_finish' => $request->date_finish,
                'location_work_id' => $request->location_work,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Store Warrant Record
             */
            if ($warrant) {
                /**
                 * Check Request File Attachment
                 */
                if (!empty($request->allFiles())) {
                    /**
                     * Attachment Path
                     */
                    $path = 'public/uploads/warrant';
                    $path_store = 'storage/uploads/warrant';

                    /**
                     * Check Last Path
                     */
                    if (!Storage::exists($path)) {
                        /**
                         * Creating New Path
                         */
                        Storage::makeDirectory($path);
                    }

                    /**
                     * File Configuration
                     */
                    $exploded_name = explode(' ', strtolower($request->name));
                    $name_warrant_config = implode('_', $exploded_name);
                    $file = $request->file('attachment');
                    $file_name = $warrant->id . '_' . $name_warrant_config . '.' . $file->getClientOriginalExtension();

                    /**
                     * Upload File
                     */
                    $file->storePubliclyAs($path, $file_name);

                    /**
                     * Validation Upload Attachment
                     */
                    if (Storage::exists($path . '/' . $file_name)) {
                        /**
                         * Update Attachment Record
                         */
                        $warrant_update = Warrant::where('id', $warrant->id)->update([
                            'attachment' => $path_store . '/' . $file_name,
                        ]);

                        /**
                         * Validation Warrant Update
                         */
                        if ($warrant_update) {
                            /**
                             * Insert Warrant User
                             */
                            foreach ($request->warrant_user as $user_id => $warrant_user) {
                                $warrant_user_request[] = [
                                    'user_id' => $user_id,
                                    'warrant_id' => $warrant->id,
                                ];
                            }

                            /**
                             * Insert Warrant User
                             */
                            $warrant_user = WarrantUser::lockForUpdate()->insert($warrant_user_request);

                            /**
                             * Validation Store Warrant User Record
                             */
                            if ($warrant_user) {
                                DB::commit();
                                return redirect()
                                    ->route('warrant.index')
                                    ->with(['success' => 'Successfully Add Warrant']);
                            } else {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Add Warrant User'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update Attachment Warrant'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Upload Attachment Warrant'])
                            ->withInput();
                    }
                } else {
                    /**
                     * Insert Warrant User
                     */
                    foreach ($request->warrant_user as $user_id => $warrant_user) {
                        $warrant_user_request[] = [
                            'user_id' => $user_id,
                            'warrant_id' => $warrant->id,
                        ];
                    }

                    /**
                     * Insert Warrant User
                     */
                    $warrant_user = WarrantUser::lockForUpdate()->insert($warrant_user_request);

                    /**
                     * Validation Store Warrant User Record
                     */
                    if ($warrant_user) {
                        DB::commit();
                        return redirect()
                            ->route('warrant.index')
                            ->with(['success' => 'Successfully Add Warrant']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Add Warrant User'])
                            ->withInput();
                    }
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Warrant'])
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
             * Get Warrant from id
             */
            $warrant = Warrant::with(['locationWork', 'presence.createdBy', 'warrantUser.user'])->find($id);

            /**
             * Validation Warrant id
             */
            if (!is_null($warrant)) {
                $data['warrant'] = $warrant;

                if (User::find(Auth::user()->id)->hasRole('admin')) {
                    $data['show_history'] = true;
                } else {
                    $data['show_history'] = false;
                }
                return view('warrant.detail', $data);
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
             * Get Warrant from id
             */
            $warrant = Warrant::with(['warrantUser.user'])->find($id);

            /**
             * Validation Warrant id
             */
            if (!is_null($warrant)) {
                $data['warrant'] = $warrant;

                /**
                 * Get All Users
                 */
                $data['users'] = User::whereNull('deleted_at')
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'staff');
                    })
                    ->get();

                /**
                 * Get All Location Work
                 */
                $data['location_works'] = LocationWork::whereNull('deleted_by')->whereNull('deleted_at')->get();
                return view('warrant.edit', $data);
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
                'date_start' => 'required|date',
                'date_finish' => 'required|date',
                'location_work' => 'required',
                'warrant_user' => 'required',
            ]);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Create Warrant Record
             */
            $warrant_update = Warrant::where('id', $id)->update([
                'name' => $request->name,
                'date_start' => $request->date_start,
                'date_finish' => $request->date_finish,
                'location_work_id' => $request->location_work,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Update Warrant Record
             */
            if ($warrant_update) {
                /**
                 * Check Request File Attachment
                 */
                if (!empty($request->allFiles())) {
                    /**
                     * Warrant Record
                     */
                    $warrant = Warrant::find($id);

                    /**
                     * Image Path
                     */
                    $path = 'public/uploads/warrant';
                    $path_store = 'storage/uploads/warrant';

                    /**
                     * Check Last Path
                     */
                    if (!Storage::exists($path)) {
                        /**
                         * Creating New Path
                         */
                        Storage::makeDirectory($path);
                    }

                    /**
                     * Last Attachment
                     */
                    $attachment_exploded = explode('/', $warrant->attachment);
                    $file_name_record = $attachment_exploded[count($attachment_exploded) - 1];

                    /**
                     * Remove Last Path
                     */
                    if (Storage::exists($path . '/' . $file_name_record)) {
                        Storage::delete($path . '/' . $file_name_record);
                    }

                    /**
                     * File Configuration
                     */
                    $exploded_name = explode(' ', strtolower($request->name));
                    $name_warrant_config = implode('_', $exploded_name);
                    $file = $request->file('attachment');
                    $file_name = $id . '_' . $name_warrant_config . '.' . $file->getClientOriginalExtension();

                    /**
                     * Upload File
                     */
                    $file->storePubliclyAs($path, $file_name);

                    /**
                     * Validation Upload Attachment
                     */
                    if (Storage::exists($path . '/' . $file_name)) {
                        /**
                         * Update Attachment Record
                         */
                        $warrant_attachment_update = Warrant::where('id', $id)->update([
                            'attachment' => $path_store . '/' . $file_name,
                        ]);

                        /**
                         * Validation Warrant Attachment Update
                         */
                        if ($warrant_attachment_update) {
                            /**
                             * Destroy Warrant User Record with Softdelete Last Record
                             */
                            $warrant_user_destroy = WarrantUser::where('warrant_id', $id)->update([
                                'deleted_at' => date('Y-m-d H:i:s'),
                            ]);

                            /**
                             * Validation Update Softdelete Warrant User Record
                             */
                            if ($warrant_user_destroy) {
                                /**
                                 * Insert Warrant User
                                 */
                                foreach ($request->warrant_user as $user_id => $warrant_user) {
                                    $warrant_user_request[] = [
                                        'user_id' => $user_id,
                                        'warrant_id' => $id,
                                    ];
                                }

                                /**
                                 * Insert Warrant User
                                 */
                                $warrant_user = WarrantUser::lockForUpdate()->insert($warrant_user_request);

                                /**
                                 * Validation Store Warrant User Record
                                 */
                                if ($warrant_user) {
                                    DB::commit();
                                    return redirect()
                                        ->route('warrant.index')
                                        ->with(['success' => 'Successfully Update Warrant']);
                                } else {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Add Warrant User'])
                                        ->withInput();
                                }
                            } else {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Destroy Warrant User'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update Attachment Warrant'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Upload Attachment Warrant'])
                            ->withInput();
                    }
                } else {
                    /**
                     * Destroy Warrant User Record with Softdelete Last Record
                     */
                    $warrant_user_destroy = WarrantUser::where('warrant_id', $id)->update([
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);

                    /**
                     * Validation Update Softdelete Warrant User Record
                     */
                    if ($warrant_user_destroy) {
                        /**
                         * Insert Warrant User
                         */
                        foreach ($request->warrant_user as $user_id => $warrant_user) {
                            $warrant_user_request[] = [
                                'user_id' => $user_id,
                                'warrant_id' => $id,
                            ];
                        }

                        /**
                         * Insert Warrant User
                         */
                        $warrant_user = WarrantUser::lockForUpdate()->insert($warrant_user_request);

                        /**
                         * Validation Store Warrant User Record
                         */
                        if ($warrant_user) {
                            DB::commit();
                            return redirect()
                                ->route('warrant.index')
                                ->with(['success' => 'Successfully Update Warrant']);
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Add Warrant User'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Destroy Warrant User'])
                            ->withInput();
                    }
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Warrant'])
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
             * Destroy Warrant Record with Softdelete
             */
            $warrant_destroy = Warrant::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Softdelete Warrant Record
             */
            if ($warrant_destroy) {
                /**
                 * Destroy Warrant User Record with Softdelete
                 */
                $warrant_user_destroy = WarrantUser::where('warrant_id', $id)->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

                /**
                 * Validation Update Softdelete Warrant User Record
                 */
                if ($warrant_user_destroy) {
                    DB::commit();
                    session()->flash('success', 'Warrant Successfully Deleted');
                } else {
                    /**
                     * Failed Update Record
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Failed Delete Warrant User');
                }
            } else {
                /**
                 * Failed Update Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Warrant');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }

    /**
     * Export list of Appoval
     */
    public function presenceWarrantExport(string $id)
    {
        try {
            /**
             * Get Warrant from id
             */
            $warrant = Warrant::with(['locationWork', 'presence.createdBy', 'warrantUser.user'])->find($id);

            /**
             * Validation Warrant id
             */
            if (!is_null($warrant)) {
                $data['warrant'] = $warrant;
                return Excel::download(new PresenceWarrantExport($data), 'Presence_' . $warrant->name . '_report.xlsx');
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
}
