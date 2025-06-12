@extends('layout.default')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Suivi des Emails d'Absence</h3>
            <a href="{{ route('reunions.index') }}" class="btn btn-light">Retour</a>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date d'envoi</th>
                            <th>Stagiaire</th>
                            <th>Email</th>
                            <th>Réunion</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emailLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ optional($log->absence->stagiaire)->nom }}</td>
                                <td>{{ $log->to_email }}</td>
                                <td>{{ optional($log->absence->reunion)->date->format('d/m/Y') }}</td>
                                <td>
                                    @if($log->status === 'sent')
                                        <span class="badge bg-success">Envoyé</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge bg-danger" title="{{ $log->error_message }}">
                                            Échec
                                        </span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-muted mb-0">Aucun email envoyé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-3">
                {{ $emailLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection