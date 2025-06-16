@extends('layout.default')

@section('title', 'Affichage des Emails logs')

@section('content')
    <h1>Journal des emails</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Stagiaire</th>
                <th>Email</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Sujet</th>
                <th>Erreur</th>
            </tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
            <tr>
                <td>{{ $log->created_at }}</td>
                <td>{{ $log->user->nom ?? '' }} {{ $log->user->prenom ?? '' }}</td>
                <td>{{ $log->to_email }}</td>
                <td>{{ $log->template->type ?? '' }}</td>
                <td>{{ $log->status }}</td>
                <td>{{ $log->subject }}</td>
                <td>{{ $log->error_message }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
@endsection