<?php

namespace App\Http\Controllers\Presence;

use App\Http\Controllers\Controller;
use App\Models\Presence\Presence;
use App\Models\User;
use App\Models\Warrant\Warrant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Route Datatable
         */
        $data['datatable_route'] = route('presence.dataTable');
        return view('presence.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * Get Warrant
         */
        $data['warrant'] = Warrant::with(['locationWork'])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->where('date_start', '<=', date('Y-m-d'))
            ->where('date_finish', '>=', date('Y-m-d'))
            ->whereHas('warrantUser', function ($query) {
                $query->whereNull('deleted_at')->where('user_id', Auth::user()->id);
            })
            ->first();

        /**
         * Get Detail User
         */
        $data['user'] = Auth::user();
        return view('presence.create', $data);
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        if (User::find(Auth::user()->id)->hasRole('admin')) {
            /**
             * Get All Presence Record
             */
            $presences = Presence::with(['warrant', 'createdBy'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->get();
        } else {
            /**
             * Get All Warrant by User
             */
            $presences = Presence::with(['warrant', 'createdBy'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('created_by', Auth::user()->id)
                ->get();
        }

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($presences)
            ->addIndexColumn()
            ->addColumn('created_at', function ($data) {
                /**
                 * Return Format Date & Time Duration
                 */
                return date('d F Y H:i:s', strtotime($data->created_at));
            })
            ->addColumn('created_by', function ($data) {
                /**
                 * Return User
                 */
                return $data->createdBy->name;
            })
            ->addColumn('warrant', function ($data) {
                /**
                 * Return Warrant Direct
                 */
                return '<a target="_blank" href="' . route('warrant.show', ['id' => $data->warrant_id]) . '">' . $data->warrant->name . '<i class="fas fa-external-link-alt ms-1"></i></a>';
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<a href="' . route('presence.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary my-1" title="Detail"><i class="fas fa-eye"></i></a>';
                return $btn_action;
            })
            ->only(['created_at', 'created_by', 'warrant', 'action'])
            ->rawColumns(['warrant', 'action'])
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
                'warrant' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'address' => 'required',
            ]);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Create Presence Record
             */
            $presence = Presence::lockforUpdate()->create([
                'warrant_id' => $request->warrant,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Store Presence Record
             */
            if ($presence) {
                /**
                 * Attachment Path
                 */
                $path = 'public/uploads/presence';
                $path_store = 'storage/uploads/presence';

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
                $image = str_replace('data:image/png;base64,', '', $request->attachment);
                $image = str_replace(' ', '+', $image);
                $exploded_name = explode(' ', strtolower(Auth::user()->name));
                $name_presence = implode('_', $exploded_name);
                $file_name = $presence->id . '_' . $request->warrant . '_' . $name_presence . '.png';
                $directory = storage_path('app/public/uploads/presence/');

                /**
                 * Check Path Exists
                 */
                if (!file_exists($directory)) {
                    mkdir($directory, 0777, true);
                }

                $path_upload = $directory . $file_name;

                /**
                 * Save and Upload Image
                 */
                $fileSaved = file_put_contents($path_upload, base64_decode($image));

                /**
                 * Validation Presence Attachment
                 */
                if ($fileSaved !== false) {
                    /**
                     * Update Presence Attachment Record
                     */
                    $presence_update = Presence::where('id', $presence->id)->update([
                        'attachment' => $path_store . '/' . $file_name,
                    ]);

                    /**
                     * Validation Presence Update
                     */
                    if ($presence_update) {
                        DB::commit();
                        return redirect()
                            ->route('presence.index')
                            ->with(['success' => 'Successfully Add Presence']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Update Attachment Presence'])
                            ->withInput();
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Upload Attachment Presence'])
                        ->withInput();
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Presence'])
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
             * Get Presence from id
             */
            $presence = Presence::with(['warrant.locationWork', 'createdBy'])->find($id);

            /**
             * Validation Presence id
             */
            if (!is_null($presence)) {
                $data['presence'] = $presence;
                return view('presence.detail', $data);
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
