<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $r)
    {
        return Booking::with('guest', 'room')
            ->when($r->search, fn ($q, $s) => $q->whereHas('guest', fn ($g) => $g
                ->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")))
            ->when($r->status, fn ($q, $s) => $q->where('status', $s))
            ->when($r->type, fn ($q, $t) => $q->whereHas('room', fn ($x) => $x->where('type', $t)))
            ->when($r->date_from, fn ($q, $d) => $q->whereDate('check_in', '>=', $d))
            ->when($r->date_to, fn ($q, $d) => $q->whereDate('check_in', '<=', $d))
            ->latest('id')->paginate(10);
    }

    public function stats()
    {
        return [
            'total' => Booking::count(),
            'occupancy' => round(Room::where('status', 'booked')->count() / max(Room::count(), 1) * 100, 1),
            'active_guests' => Booking::where('status', 'checked-in')->count(),
            'revenue' => (float) Booking::where('status', '!=', 'cancelled')->sum('total_amount'),
        ];
    }

    public function options()
    {
        return ['guests' => Guest::orderBy('name')->get(['id', 'name']), 'rooms' => Room::orderBy('room_number')->get(['id', 'room_number', 'type'])];
    }

    public function store(Request $r) { return $this->save($r); }
    public function update(Request $r, Booking $booking) { return $this->save($r, $booking); }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->noContent();
    }

    private function save(Request $r, ?Booking $b = null)
    {
        $d = $r->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'status' => 'required|in:confirmed,checked-in,checked-out,cancelled',
        ]);

        $clash = Booking::where('room_id', $d['room_id'])->where('status', '!=', 'cancelled')
            ->when($b, fn ($q) => $q->where('id', '!=', $b->id))
            ->where('check_in', '<', $d['check_out'])->where('check_out', '>', $d['check_in'])->exists();
        if ($clash && $d['status'] !== 'cancelled') {
            throw ValidationException::withMessages(['room_id' => 'Room is already booked for these dates.']);
        }

        $nights = max(1, (int) Carbon::parse($d['check_in'])->diffInDays(Carbon::parse($d['check_out'])));
        $d['total_amount'] = $nights * Room::find($d['room_id'])->price_per_night;

        $b ? $b->update($d) : $b = Booking::create($d);
        return $b->load('guest', 'room');
    }
}
