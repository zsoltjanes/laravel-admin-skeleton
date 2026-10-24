<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Users')]
class Index extends Component
{
    use WithPagination;

    /** Columns the list can be sorted by. */
    private const SORTABLE = ['name', 'email', 'created_at'];

    private const PER_PAGE_OPTIONS = [10, 25, 50];

    #[Url(except: '')]
    public string $search = '';

    /** '', 'verified', 'unverified' or 'two_factor' */
    #[Url(except: '')]
    public string $status = '';

    #[Url(except: 'created_at')]
    public string $sortBy = 'created_at';

    #[Url(except: 'desc')]
    public string $sortDirection = 'desc';

    #[Url(except: 10)]
    public int $perPage = 10;

    /** @var array<int, string> */
    public array $selected = [];

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'perPage'], true)) {
            $this->resetPage();
            $this->selected = [];
        }
    }

    public function sort(string $column): void
    {
        if (! in_array($column, self::SORTABLE, true)) {
            return;
        }

        $this->sortDirection = $this->sortBy === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
    }

    public function delete(int $id): void
    {
        $this->deleteUsers([$id]);
    }

    public function deleteSelected(): void
    {
        $this->deleteUsers(array_map('intval', $this->selected));
        $this->selected = [];
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $sortBy = in_array($this->sortBy, self::SORTABLE, true) ? $this->sortBy : 'created_at';
        $perPage = in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : 10;

        return User::query()
            ->when($this->search !== '', fn (Builder $query) => $query->where(
                fn (Builder $query) => $query
                    ->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
            ))
            ->when($this->status === 'verified', fn (Builder $query) => $query->whereNotNull('email_verified_at'))
            ->when($this->status === 'unverified', fn (Builder $query) => $query->whereNull('email_verified_at'))
            ->when($this->status === 'two_factor', fn (Builder $query) => $query->whereNotNull('two_factor_confirmed_at'))
            ->orderBy($sortBy, $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage);
    }

    /**
     * @return array{total: int, verified: int, unverified: int, two_factor: int, new_this_month: int}
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'two_factor' => User::whereNotNull('two_factor_confirmed_at')->count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * @return array<int, int>
     */
    public function perPageOptions(): array
    {
        return self::PER_PAGE_OPTIONS;
    }

    public function render(): View
    {
        return view('admin.users.index');
    }

    /**
     * Delete the given users, never the one who is logged in.
     *
     * @param  array<int, int>  $ids
     */
    private function deleteUsers(array $ids): void
    {
        $ids = array_values(array_diff($ids, [Auth::id()]));

        if ($ids === []) {
            Flux::toast(variant: 'warning', text: __('You cannot delete your own account here.'));

            return;
        }

        $count = User::whereKey($ids)->delete();

        unset($this->users, $this->stats);

        Flux::toast(variant: 'success', text: trans_choice(':count user deleted.|:count users deleted.', $count, ['count' => $count]));
    }
}
