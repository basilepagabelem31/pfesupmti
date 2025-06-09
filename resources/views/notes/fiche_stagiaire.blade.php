<!--detaille de note de ce stagiaire en question  --->
@extends('layout.default')

@section('title', 'Fiche du stagiaire')

@section('content')

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2>Notes de {{ $stagiaire->nom }} {{ $stagiaire->prenom }}</h2>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
                @include('notes.partials.form', ['stagiaire' => $stagiaire])
            @endif

            <hr>
            <h4>Historique des notes</h4>
            <ul class="list-group">
            @forelse($notes as $note)
                @php
                    $peutVoir = false;
                    $user = Auth::user();
                    if ($note->visibilite === 'all') $peutVoir = true;
                    elseif ($note->visibilite === 'donneur' && $note->donneur_id === $user->id) $peutVoir = true;
                    elseif ($note->visibilite === 'donneur + stagiaire' && ($note->donneur_id === $user->id || $note->stagiaire_id === $user->id)) $peutVoir = true;
                    elseif ($note->visibilite === 'superviseurs- stagiaire' && ($user->isSuperviseur() || $user->isAdministrateur())) $peutVoir = true;
                @endphp
                @if($peutVoir)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ \Carbon\Carbon::parse($note->date_note)->format('d/m/Y') }}</strong> :
                            {{ $note->valeur }}
                            <br>
                            <small>
                                Visibilité : <em>{{ $note->visibilite }}</em> |
                                Donneur : <em>{{ $note->donneur->nom ?? '' }} {{ $note->donneur->prenom ?? '' }}</em>
                            </small>
                        </div>
                        @if(Auth::id() === $note->donneur_id)
                            <div>
                                <a href="{{ route('notes.edit', $note->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                                <form action="{{ route('notes.destroy', $note->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Voulez-vous vraiment supprimer cette note ?')">Supprimer</button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endif
            @empty
                <li class="list-group-item">Aucune note pour ce stagiaire.</li>
            @endforelse
            </ul>

            @if(Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
                <a href="{{ route('notes.liste_stagiaires') }}" class="btn btn-secondary mt-3">
                    <i class="fa fa-arrow-left"></i> Retour à la liste
                </a>
            @endif
        </div>
    </div>
</div>
@endsection