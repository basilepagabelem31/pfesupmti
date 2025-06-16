@extends('layout.default')

@section('title', 'Email Settings ')

@section('content')
   <div class="container py-4">
    <h1 class="mb-4">Paramètres Email</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table align-middle">
                <tr><th>Protocole</th><td>{{ $settings->protocole ?? '-' }}</td></tr>
                <tr><th>Host</th><td>{{ $settings->host ?? '-' }}</td></tr>
                <tr><th>Port</th><td>{{ $settings->port ?? '-' }}</td></tr>
                <tr><th>Username</th><td>{{ $settings->username ?? '-' }}</td></tr>
                <tr><th>Mot de passe </th><td>{{ $settings->password ?? '-' }}</td></tr>
                <tr><th>Email expéditeur</th><td>{{ $settings->from_address ?? '-' }}</td></tr>
                <tr><th>Nom expéditeur</th><td>{{ $settings->from_name ?? '-' }}</td></tr>
                <tr><th>Encryption</th><td>{{ $settings->encryption ?? '-' }}</td></tr>
            </table>
            <div class="mt-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                    {{ $settings ? 'Modifier' : 'Créer' }} les paramètres
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.email-settings.update') }}" class="modal-content">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">{{ $settings ? 'Modifier' : 'Créer' }} les paramètres email</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Protocole</label>
                <input type="text" class="form-control" name="protocole" value="{{ old('protocole', $settings->protocole ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Host</label>
                <input type="text" class="form-control" name="host" value="{{ old('host', $settings->host ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Port</label>
                <input type="number" class="form-control" name="port" value="{{ old('port', $settings->port ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="{{ old('username', $settings->username ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password" value="" {{ !$settings ? 'required' : '' }}>
                @if($settings)
                <small class="text-muted">Laisse vide pour ne pas modifier le mot de passe.</small>
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label">Encryption</label>
                <input type="text" class="form-control" name="encryption" value="{{ old('encryption', $settings->encryption ?? '') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Email expéditeur</label>
                <input type="email" class="form-control" name="from_address" value="{{ old('from_address', $settings->from_address ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nom expéditeur</label>
                <input type="text" class="form-control" name="from_name" value="{{ old('from_name', $settings->from_name ?? '') }}" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
  </div>
</div>
@endsection