<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingStoreRequest;
use App\Models\Booking;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookings = Booking::all();
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tables = Table::where('status', TableStatus::Available)->get();
        return view('admin.bookings.create', compact('tables'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookingStoreRequest $request)
    {
        $table = Table::findOrFail($request->table_id);
        if ($request->rooms_number > $table->rooms_number) {
            return back()->with('warning', 'Please choose the house based on rooms.');
        }
        $request_date = Carbon::parse($request->book_date);
        foreach ($table->bookings as $book) {
            if ($book->book_date->format('Y-m-d') == $request_date->format('Y-m-d')) {
                return back()->with('warning', 'This house is booked for this date.');
            }
        }
        Booking::create($request->validated());

        return to_route('admin.bookings.index')->with('success', 'Booking created successfully.');
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
    public function edit(Booking $booking)
    {
        $tables = Table::where('status', TableStatus::Available)->get();
        return view('admin.bookings.edit', compact('booking', 'tables'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookingStoreRequest $request, Booking $booking)
    {
        $table = Table::findOrFail($request->table_id);
        if ($request->rooms_number > $table->rooms_number) {
            return back()->with('warning', 'Please choose the table base on rooms.');
        }
        $request_date = Carbon::parse($request->book_date);
        $bookings = $table->bookings()->where('id', '!=', $booking->id)->get();
        foreach ($bookings as $book) {
            if ($book->book_date->format('Y-m-d') == $request_date->format('Y-m-d')) {
                return back()->with('warning', 'This table is booked for this date.');
            }
        }

        $booking->update($request->validated());
        return to_route('admin.bookings.index')->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return to_route('admin.bookings.index')->with('warning', 'Booking deleted successfully.');
    }
}

