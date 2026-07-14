# SIAKAD NUJA — View Rebuild Style Guide (Tailwind v4 + Alpine)

Rebuild Blade views from Bootstrap to Tailwind using ONLY the existing component
library. Match the look of `resources/views/dashboard.blade.php` (after rebuild)
and the components. Bahasa Indonesia labels. Mobile-first. Dark mode aware.

## Golden rules
- Every page: `@extends('layouts.app')`, `@section('title', '...')`, `@section('content') ... @endsection`.
- The layout already renders flash `success`/`error` and provides sidebar/topbar. Do NOT re-add them.
- NEVER use Bootstrap classes (`card`, `btn`, `row`, `col-md-*`, `table`, `badge bg-*`, `bi bi-*`, `form-control`, `form-select`, `d-flex`, `text-muted`, `mb-3`). NEVER use `<i class="bi ...">` — use `<x-icon name="..."/>`.
- Preserve the EXACT data contract: same `$variables`, same DB columns, same route names, same relations as the controller passes / the old view used. Read the controller + model + migration + old view before writing.
- Respect role visibility: wrap write actions (create/edit/delete buttons) in `@if(auth()->user()->isAdmin())` for master-data modules where guru is read-only (siswa, kelas, mata-pelajaran, jadwal, pengumuman, orang-tua, guru, users). Nilai & Absensi write actions are allowed for both roles (policy-guarded) — do NOT hide those, but the guru's own scoping is handled server-side.

## Route parameter quirks (Laravel singularization)
- `mata-pelajaran` resource → param is `$mataPelajaran`; routes `mata-pelajaran.*`.
- `orang-tua` resource → param `$orangTua`; routes `orang-tua.*`.
- `kelas` → route model binding param `{kela}` internally but route names are `kelas.*`; pass the model instance to `route('kelas.show', $k)`.
- `users` routes are `users.*` (index/create/store/edit/update/destroy — NO show).
- Always pass model instances to route() helpers (e.g. `route('siswa.edit', $s)`).

## Component API (use these, do not reinvent)

### Layout / structure
- `<x-page-header title="Data Siswa" subtitle="...optional...">` with optional `<x-slot:actions> ... </x-slot:actions>` for top-right buttons.
- `<x-card>` … `</x-card>`. Optional `<x-slot:header>`/`<x-slot:footer>`. Prop `padding` (default `p-5 sm:p-6`); use `padding="p-0"` when wrapping a full-bleed table.
- `<x-stat-card label="Total Siswa" :value="$n" icon="siswa" color="brand" :href="route('siswa.index')" />`. colors: brand, emerald, amber, sky, rose.
- `<x-empty-state icon="siswa" title="Belum ada data" description="..."/>` with optional `<x-slot:action>`.

### Buttons & badges
- `<x-button variant="primary|secondary|danger|ghost|success" size="sm|md|lg|icon" :href="route(...)">`. Use `href` to render `<a>`; omit for `<button>` (set `type="submit"`).
- Icon-only action buttons in tables: `<x-button :href="route('x.show',$m)" variant="ghost" size="icon"><x-icon name="eye" class="h-4 w-4"/></x-button>` (edit → icon `edit`).
- `<x-badge variant="slate|brand|success|warning|danger|info">Teks</x-badge>`.

### Delete confirmation (replaces Bootstrap confirm())
`<x-confirm-delete :action="route('siswa.destroy', $s)" />` — renders a red trash icon-button that opens an Alpine modal. Use this for ALL deletes; never use `onsubmit="return confirm()"`.

### Forms
Put inside `<form method="POST" action="..." class="space-y-...">` + `@csrf` (+ `@method('PUT')` on edit). For file upload add `enctype="multipart/form-data"`.
- `<x-form.input label="NIS" name="nis" :value="$siswa->nis ?? ''" required hint="..."/>` (type prop: text/number/email/date/password; date value must be `optional($m->col)->format('Y-m-d')`).
- `<x-form.select label="Kelas" name="kelas_id" :selected="old('kelas_id', $siswa->kelas_id ?? '')" required>` then `<option>` list inside using `@selected(...)`. Set `:placeholder="false"` to omit the blank option; default placeholder is "— Pilih —".
- `<x-form.textarea label="Alamat" name="alamat" :value="$siswa->alamat ?? ''" rows="3"/>`.
- `<x-form.checkbox label="Aktif" name="is_active" :checked="old('is_active', $user->is_active ?? true)"/>` (renders hidden 0 + checkbox 1 automatically).
- Components auto-render validation errors + old() values. Do NOT add manual `@error` blocks or `value="{{ old(...) }}"` on top of the components — pass `:value`/`:selected` for the model value only.
- Form layout: `<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">` for two-column desktop / one-column mobile. Full-width fields: add `class="sm:col-span-2"` on the field wrapper — wrap the component in a `<div class="sm:col-span-2">`.
- Form actions row: `<div class="flex items-center gap-3 pt-2">` with primary submit `<x-button type="submit" variant="primary">Simpan</x-button>` and `<x-button variant="secondary" :href="route('x.index')">Batal</x-button>`.

## Index page pattern (responsive table + mobile cards)
```
@extends('layouts.app')
@section('title', 'Data X')
@section('content')
<x-page-header title="Data X" subtitle="...">
    @if(auth()->user()->isAdmin())
    <x-slot:actions>
        <x-button :href="route('x.create')" variant="primary"><x-icon name="plus" class="h-4 w-4"/> Tambah</x-button>
    </x-slot:actions>
    @endif
</x-page-header>

{{-- optional filter/search --}}
<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
    <x-search-bar placeholder="Cari nama atau NIS..." />
    {{-- extra filter selects as plain <form>/<select> submitting GET if needed --}}
</div>

<x-card padding="p-0">
    @if($items->count())
        {{-- Desktop table --}}
        <div class="hidden md:block">
            <x-table :headers="['NIS','Nama','Kelas','Aksi']">
                @foreach($items as $it)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="px-4 py-3 text-sm ...">{{ $it->nis }}</td>
                    ...
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <x-button :href="route('x.show',$it)" variant="ghost" size="icon"><x-icon name="eye" class="h-4 w-4"/></x-button>
                            @if(auth()->user()->isAdmin())
                            <x-button :href="route('x.edit',$it)" variant="ghost" size="icon"><x-icon name="edit" class="h-4 w-4"/></x-button>
                            <x-confirm-delete :action="route('x.destroy',$it)" />
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-table>
        </div>
        {{-- Mobile cards --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 md:hidden">
            @foreach($items as $it)
            <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-900 dark:text-white truncate">{{ $it->nama }}</p>
                        <p class="text-sm text-slate-500">{{ $it->nis }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">... same actions ...</div>
                </div>
                <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                    <div><dt class="text-slate-400 text-xs">Kelas</dt><dd>{{ ... }}</dd></div>
                </dl>
            </div>
            @endforeach
        </div>
    @else
        <div class="p-6"><x-empty-state icon="x" title="Belum ada data" /></div>
    @endif
</x-card>

@if($items->hasPages())
    <div class="mt-4">{{ $items->withQueryString()->links() }}</div>
@endif
@endsection
```
Table cell base classes: `px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap`. Primary name cell: add `font-medium text-slate-900 dark:text-white`.

## Create / Edit page pattern
```
@extends('layouts.app')
@section('title', 'Tambah X')
@section('content')
<x-page-header title="Tambah X" subtitle="..." />
<x-card>
    <form method="POST" action="{{ route('x.store') }}" class="space-y-6">
        @csrf
        @include('x._form')
    </form>
</x-card>
@endsection
```
Edit uses `route('x.update',$m)` + `@method('PUT')` right after `@csrf`. The `_form` partial contains only the fields grid + the actions row (NO `@csrf`, NO `<form>` tag — those live in create/edit). NOTE: some existing `_form` partials include `@csrf` and the submit button; when rebuilding, move `@csrf` OUT to the create/edit wrapper OR keep it in `_form` — pick ONE and be consistent: **put `@csrf`/`@method` and `<form>` in create.blade.php & edit.blade.php, and keep ONLY fields + actions in `_form.blade.php`.**

## Show page pattern
```
<x-page-header title="Detail X"><x-slot:actions>... edit/back buttons ...</x-slot:actions></x-page-header>
<x-card>
  <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
    <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">NIS</dt>
         <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $m->nis }}</dd></div>
  </dl>
</x-card>
```
For related lists (e.g. siswa → nilai/absensi/orang tua), use additional `<x-card>` blocks with small `<x-table>`s.

## Status → badge mapping
- Siswa status: Aktif=success, Lulus=info, Pindah=warning, Keluar=danger.
- Absensi status: Hadir=success, Izin=info, Sakit=warning, Alpa=danger.
- Predikat nilai: A/B=success, C=info, D=warning, E=danger (or brand for all).
- Pengumuman is_active: true=success "Aktif", false=slate "Nonaktif". target_role badge=brand.
- User role: admin=brand, guru=info. is_active: success/slate.

## Available x-icon names
dashboard, siswa, guru, kelas, mapel, jadwal, nilai, absensi, orangtua, pengumuman, users, logout, plus, edit, trash, eye, search, menu, close, sun, moon, chevron-down, user, clock, check, building. (Fallback icon = siswa.) Use the module's own icon for empty states / headers.
