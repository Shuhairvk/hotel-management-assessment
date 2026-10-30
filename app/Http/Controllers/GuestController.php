<?php
namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    public function index(Request $r)
    {
        return Guest::when($r->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%")->orWhere('nationality', 'like', "%$s%")))
            ->latest('id')->paginate(10);
    }

    public function store(Request $r)
    {
        $d = $r->validate($this->rules());
        if ($r->hasFile('photo')) $d['photo'] = $r->file('photo')->store('guests', 'public');
        return Guest::create($d);
    }

    public function update(Request $r, Guest $guest)
    {
        $d = $r->validate($this->rules($guest->id));
        if ($r->hasFile('photo')) {
            if ($guest->photo) Storage::disk('public')->delete($guest->photo);
            $d['photo'] = $r->file('photo')->store('guests', 'public');
        } else {
            unset($d['photo']);
        }
        $guest->update($d);
        return $guest;
    }

    public function destroy(Guest $guest)
    {
        if ($guest->photo) Storage::disk('public')->delete($guest->photo);
        $guest->delete();
        return response()->noContent();
    }

    private function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('guests')->ignore($id)],
            'phone' => 'required|string|max:30',
            'nationality' => 'required|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
