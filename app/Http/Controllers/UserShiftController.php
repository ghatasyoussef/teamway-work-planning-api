<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserShift;
use Illuminate\Http\Request;

/**
 * UserShiftController: A controller than handles requests related to the shifts.
 */
class UserShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return UserShift::with('user')->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request: The request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $numberOfShiftsPerDay = (count(config('shifts.shifts_per_day')) - 1);

        $validatedRequest = $request->validate([
            'shift_number' => ['required', 'integer', 'min:0', 'max:3'],
            'worker_id' => ['required'],
            'day' => ['date',  'required'],
        ]);

        $this->checkShift($request);

        $addedShift = UserShift::create($validatedRequest);

        // return $d;
        return UserShift::where('id', $addedShift->id)->with('user')->get();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id: ID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return UserShift::where('id', $id)->with('user')->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id: ID
     * @param  Request  $request: The request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check the shift exists.

        $userShift = UserShift::where('id', $id)->first();
        if (! $userShift) {
            abort(404, "Cannot find any Shift with ID = {$id}");
        }
        $ignoreDay = false;
        // If the day isn't changed, ignore it in checks.
        if ($userShift->day == $request->day) {

            $ignoreDay = true;
        }

        $validatedRequest = $request->validate([
            'shift_number' => ['required_without_all:day,worker_id', 'integer', 'min:0', 'max:3'],
            'worker_id' => ['required_without_all:day,shift_number'],
            'day' => ['date',  'required_without_all:worker_id,shift_number'],
        ]);

        $userShift->fill($validatedRequest);
        $this->checkShift($userShift, $ignoreDay);

        $userShift->save();

        return UserShift::where('id', $userShift->id)->with('user')->get();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  UserShift  $shift: User Shift Object.
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserShift $shift)
    {
        $shift->delete();

        return "Shift with id = {$shift->id} is deleted successfully!";

    }

    /**
     * Search for User Shifts with a required criteria.
     *
     *
     * If mulitple conditions are added they will be anded
     * Avaiable criteria filters:
     *          email: worker email to get the shifts for.
     *          name: worker name to get the shifts for.
     *          worker_id: a wokrer ID to get the shifts for.
     *          day: a date of a day to get the shifts for.
     *          shift_number: the shift number. For the 3 shifts, the number is between 0-2
     *
     * @param  int  $id: ID
     * @param  mixed  $request: The request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $request->validate([
            'email' => ['required_without_all:name,day,worker_id,shift_number'],
            'name' => ['required_without_all:email,day,worker_id,shift_number'],
            'day' => ['required_without_all:email,name,worker_id,shift_number'],
            'worker_id' => ['required_without_all:email,name,day,shift_number'],
            'shift_number' => ['required_without_all:email,name,day,worker_id'],
        ]);
        $shiftQuery = UserShift::query();
        if (isset($request->email) || isset($request->name)) {
            $shiftQuery->whereHas('user', function ($query) use ($request) {
                if (isset($request->email)) {
                    $query->where('email', 'like', '%' . $request->email . '%');
                }
                if (isset($request->name)) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }

            });

        }

        if (isset($request->day)) {
            $shiftQuery->where('day', $request->day);
        }
        if (isset($request->worker_id)) {
            $shiftQuery->where('worker_id', $request->worker_id);
        }
        if (isset($request->shift_number)) {
            $shiftQuery->where('shift_number', $request->shift_number);

        }

        return $shiftQuery->with('user')->paginate();

    }

    /**
     * Check that a shift can be added with the given parameters.
     * For any problem, it aborts with the corresponding status code and message.
     *
     * @param  Request  $request: The request
     * @param  bool  $ignoreDay: True to ignore checking number of shifts per day. Usable in update when day isn't changed.
     * @return mixed the validated request
     */
    private function checkShift($request, $ignoreDay = false, $userShift = null)
    {
        $maxShiftsPerWorkerPerDay = config('shifts.max_shifts_per_worker_per_day');
        $user = User::where('id', $request->worker_id)->first();
        if (! $user) {
            abort(400, 'Worker not found!');
        }

        // Check worker doesn't have another shift in the day
        if (! $ignoreDay) {
            $userExistingShiftsForThatDay = UserShift::where('worker_id', $request->worker_id)->where('day', $request->day)->count();
            if ($userExistingShiftsForThatDay >= $maxShiftsPerWorkerPerDay) {
                abort(409, "Worker already have another shift on that day. Choose another day, or remove the worker's shift first");
            }

        }

    }
}
