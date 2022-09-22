<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Table;
use App\Rules\DateBetween;
use App\Rules\TimeBetween;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function stepOne(Request $request)
    {
        $booking = $request->session()->get('booking');
        $min_date = Carbon::today();
        $max_date = Carbon::now()->addWeek();
        return view('bookings.step-one', compact('booking', 'min_date', 'max_date'));
    }
    public function storeStepOne(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'book_date' => ['required', 'date', new DateBetween, new TimeBetween],
            'tel_number' => ['required'],
            'rooms_number' => ['required'],
        ]);

        if (empty($request->session()->get('booking'))) {
            $booking = new Booking();
            $booking->fill($validated);
            $request->session()->put('booking', $booking);
        } else {
            $booking = $request->session()->get('booking');
            $booking->fill($validated);
            $request->session()->put('booking', $booking);
        }

        return to_route('bookings.step.two');
    }

    public function stepTwo(Request $request)
    {
        $booking = $request->session()->get('booking');
        $book_table_ids = Booking::orderBy('book_date')->get()->filter(function ($value) use ($booking) {
            return $value->book_date->format('Y-m-d') == $booking->book_date->format('Y-m-d');
        })->pluck('table_id');
        $tables = Table::where('status', TableStatus::Available)
            ->where('rooms_number', '>=', $booking->rooms_number)
            ->whereNotIn('id', $book_table_ids)->get();
        return view('bookings.step-two', compact('booking', 'tables'));
    }

    public function storeStepTwo(Request $request)
    {
        $validated = $request->validate([
            'table_id' => ['required']
        ]);
        $booking = $request->session()->get('booking');
        $booking->fill($validated);
        $booking->save();
        $request->session()->forget('booking');

        return to_route('thankyou');
    }
}
