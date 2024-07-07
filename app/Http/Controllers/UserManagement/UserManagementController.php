<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Division\Division;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Route Datatable
         */
        $data['datatable_route'] = route('user-management.dataTable');
        return view('user_management.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * Get All Role
         */
        $data['roles'] = Role::all();

        /**
         * Get All Division
         */
        $data['divisions'] = Division::whereNull('deleted_at')->get();
        return view('user_management.create', $data);
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All User
         */
        $users = User::with(['division'])
            ->whereNull('deleted_at')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role', function ($data) {
                /**
                 * User Role Configuration
                 */
                $exploded_raw_role = explode('-', $data->getRoleNames()[0]);
                $user_role = ucwords(implode(' ', $exploded_raw_role));
                return $user_role;
            })
            ->addColumn('division', function ($data) {
                /**
                 * Check Field Division
                 */
                if (!is_null($data->division_id)) {
                    return $data->division->name;
                } else {
                    return null;
                }
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<a href="' . route('user-management.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary my-1" title="Detail"><i class="fas fa-eye"></i></a>';
                $btn_action .= '<a href="' . route('user-management.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning my-1 ms-1" title="Ubah"><i class="fas fa-pencil-alt"></i></a>';

                /**
                 * Validation User Logged In Equals with User Record id
                 */
                if (Auth::user()->id != $data->id) {
                    $btn_action .= '<button class="btn btn-sm btn-danger my-1 ms-1" onclick="destroy(' . $data->id . ')" title="Hapus"><i class="fas fa-trash"></i></button>';
                }
                return $btn_action;
            })
            ->only(['employee_number', 'name', 'role', 'division', 'action'])
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
                'name' => 'required|string',
                'employee_number' => 'required',
                'email' => 'required|email',
                'username' => 'required',
                'roles' => 'required',
                'password' => 'required',
                're_password' => 'required|same:password',
            ]);

            /**
             * Validation Unique Field Record
             */
            $employee_number_check = User::whereNull('deleted_at')
                ->where('username', $request->employee_number)
                ->first();
            $username_check = User::whereNull('deleted_at')
                ->where('username', $request->username)
                ->first();
            $email_check = User::whereNull('deleted_at')
                ->where('email', $request->email)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($employee_number_check) && is_null($username_check) && is_null($email_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create User Record
                 */
                $user = User::lockforUpdate()->create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'employee_number' => $request->employee_number,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'division_id' => $request->division,
                ]);

                /**
                 * Assign Role of User Based on Requested
                 */
                $model_has_role = $user->assignRole($request->roles);

                /**
                 * Validation Create User Record and Assign Role User
                 */
                if ($user && $model_has_role) {
                    DB::commit();
                    return redirect()
                        ->route('user-management.index')
                        ->with(['success' => 'Successfully Add User']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add User'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Email or Username or Employee Number Already Exist'])
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
             * Get User Record from id
             */
            $user = User::with(['division'])->find($id);

            /**
             * Validation User id
             */
            if (!is_null($user)) {
                $data['user'] = $user;

                /**
                 * User Role Configuration
                 */
                $exploded_raw_role = explode('-', $user->getRoleNames()[0]);
                $data['user_role'] = ucwords(implode(' ', $exploded_raw_role));

                return view('user_management.detail', $data);
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
             * Get User Record from id
             */
            $user = User::with(['division'])->find($id);

            /**
             * Validation User id
             */
            if (!is_null($user)) {
                $data['user'] = $user;

                /**
                 * Get All Role
                 */
                $data['roles'] = Role::all();

                /**
                 * Get All Division
                 */
                $data['divisions'] = Division::whereNull('deleted_at')->get();

                /**
                 * Disabled Edit Role with Same User Logged in
                 */
                $data['role_disabled'] = $id == Auth::user()->id ? 'disabled' : '';

                return view('user_management.edit', $data);
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
                'name' => 'required|string',
                'email' => 'required|email',
                'username' => 'required',
                'roles' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $employee_number_check = User::whereNull('deleted_at')
                ->where('username', $request->employee_number)
                ->where('id', '!=', $id)
                ->first();
            $username_check = User::whereNull('deleted_at')
                ->where('username', $request->username)
                ->where('id', '!=', $id)
                ->first();
            $email_check = User::whereNull('deleted_at')
                ->where('email', $request->email)
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($employee_number_check) && is_null($username_check) && is_null($email_check)) {
                /**
                 * Get User Record from id
                 */
                $user = User::find($id);

                /**
                 * Validation User id
                 */
                if (!is_null($user)) {
                    /**
                     * Validation Password Request
                     */
                    if (isset($request->password)) {
                        /**
                         * Validation Request Body Variables
                         */
                        $request->validate([
                            'password' => 'required',
                            're_password' => 'required|same:password',
                        ]);

                        /**
                         * Begin Transaction
                         */
                        DB::beginTransaction();

                        /**
                         * Update User Record
                         */
                        $user_update = User::where('id', $id)->update([
                            'name' => $request->name,
                            'username' => $request->username,
                            'employee_number' => $request->employee_number,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
                            'division_id' => $request->division,
                        ]);
                    } else {
                        /**
                         * Begin Transaction
                         */
                        DB::beginTransaction();

                        /**
                         * Update User Record
                         */
                        $user_update = User::where('id', $id)->update([
                            'name' => $request->name,
                            'username' => $request->username,
                            'employee_number' => $request->employee_number,
                            'email' => $request->email,
                            'division_id' => $request->division,
                        ]);
                    }

                    /**
                     * Validation Update Role Equals Default
                     */
                    if ($user->getRoleNames()[0] != $request->roles) {
                        /**
                         * Assign Role of User Based on Requested
                         */
                        $model_has_role_delete = $user->removeRole($user->getRoleNames()[0]);

                        /**
                         * Assign Role of User Based on Requested
                         */
                        $model_has_role_update = $user->assignRole($request->roles);

                        /**
                         * Validation Update User Record and Update Assign Role User
                         */
                        if ($user_update && $model_has_role_delete && $model_has_role_update) {
                            DB::commit();
                            return redirect()
                                ->route('user-management.index')
                                ->with(['success' => 'Successfully Update User']);
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update User'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Validation Update User Record
                         */
                        if ($user_update) {
                            DB::commit();
                            return redirect()
                                ->route('user-management.index')
                                ->with(['success' => 'Successfully Update User']);
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update User'])
                                ->withInput();
                        }
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Email or Username or Employee Number Already Exist'])
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
             * Destroy User Record with Softdelete
             */
            $user_destroy = User::where('id', $id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Softdelete User Record
             */
            if ($user_destroy) {
                DB::commit();
                session()->flash('success', 'User Successfully Deleted');
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
