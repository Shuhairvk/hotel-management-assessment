<?php
namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $r)
    {
        return Room::when($r->search, fn ($q, $s) => $q->where('room_number', 'like', "%$s%"))
            ->when($r->type, fn ($q, $t) => $q->where('type', $t))
            ->when($r->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('room_number')->paginate(10);
    }

    public function store(Request $r) { return Room::create($r->validate($this->rules())); }

    public function update(Request $r, Room $room)
    {
        $room->update($r->validate($this->rules($room->id)));
        return $room;
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return response()->noContent();
    }

    private function rules($id = null): array
    {
        return [
            'room_number' => ['required', 'string', 'max:20', Rule::unique('rooms')->ignore($id)],
            'type' => 'required|in:Standard,Deluxe,Suite',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,booked',
        ];
    }
}
