@extends('layouts.app') {{-- Assurez-vous d'avoir un layout approprié --}}

@section('content')
<div class="container">
    <h1>Mes Demandes de Coéquipiers</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('demande_coequipiers.create') }}" class="btn btn-primary">Envoyer une nouvelle demande</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2>Demandes Reçues</h2>
            @if ($demandesRecues->isEmpty())
                <p>Aucune demande de coéquipier reçue pour l'instant.</p>
            @else
                <ul class="list-group">
                    @foreach ($demandesRecues as $demande)
                        <li class="list-group-item">
                            De: **{{ $demande->demandeur->nom }} {{ $demande->demandeur->prenom }}** ({{ $demande->demandeur->email }})<br>
                            Statut: <span class="badge {{ $demande->statut_demande === 'en_attente' ? 'bg-warning' : ($demande->statut_demande === 'acceptée' ? 'bg-success' : 'bg-danger') }}">{{ $demande->statut_demande }}</span><br>
                            Date: {{ $demande->date_demande->format('d/m/Y H:i') }}

                            @if ($demande->statut_demande === 'en_attente')
                                <div class="mt-2">
                                    <form action="{{ route('demande_coequipiers.accept', $demande) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Accepter</button>
                                    </form>
                                    <form action="{{ route('demande_coequipiers.refuse', $demande) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
                                    </form>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="col-md-6">
            <h2>Demandes Envoyées</h2>
            @if ($demandesEnvoyees->isEmpty())
                <p>Aucune demande de coéquipier envoyée pour l'instant.</p>
            @else
                <ul class="list-group">
                    @foreach ($demandesEnvoyees as $demande)
                        <li class="list-group-item">
                            À: **{{ $demande->receveur->nom }} {{ $demande->receveur->prenom }}** ({{ $demande->receveur->email }})<br>
                            Statut: <span class="badge {{ $demande->statut_demande === 'en_attente' ? 'bg-warning' : ($demande->statut_demande === 'acceptée' ? 'bg-success' : 'bg-danger') }}">{{ $demande->statut_demande }}</span><br>
                            Date: {{ $demande->date_demande->format('d/m/Y H:i') }}

                            @if ($demande->statut_demande === 'en_attente')
                                <div class="mt-2">
                                    <form action="{{ route('demande_coequipiers.cancel', $demande) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm">Annuler</button>
                                    </form>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection