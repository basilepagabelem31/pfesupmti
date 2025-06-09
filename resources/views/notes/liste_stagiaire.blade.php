<!--liste des stagiaires qui ont une note --->
@extends('layout.default')

@section('title', 'Liste des stagiaires')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h1>Liste des stagiaires</h1>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Nombre de notes</th>
                        <th>Dernière note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($stagiaires as $stagiaire)
                    <tr>
                        <td>{{ $stagiaire->nom }}</td>
                        <td>{{ $stagiaire->prenom }}</td>
                        <td><span class="badge bg-info">{{ $stagiaire->notes_count }}</span></td>
                        <td>
                            @php
                                $lastNote = $stagiaire->notes()->orderByDesc('date_note')->first();
                            @endphp
                            {{ $lastNote ? Str::limit($lastNote->valeur, 40) : 'aucune note' }}
                        </td>
                        <td>
                            <a href="{{ route('notes.fiche_stagiaire', $stagiaire->id) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> Voir les notes
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection