@extends('layout.default')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-7xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-3xl"></div>

            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 mt-4">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight text-center sm:text-left mb-4 sm:mb-0">
                    Suivi des <span class="text-indigo-600">Emails d'Absence</span>
                </h1>
                <a href="{{ route('reunions.index') }}" class="px-6 py-3 border border-gray-300 text-base font-semibold rounded-xl shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out uppercase tracking-wider">
                    <i class="fas fa-arrow-alt-circle-left mr-2"></i> Retour aux Réunions
                </a>
            </div>
            
            <div class="p-0"> {{-- bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-100 --}}
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                        <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="font-semibold text-lg">{{ session('success') }}</span>
                    </div>
                @endif
                {{-- Ajout du message d'erreur si nécessaire, similaire à promotions.index --}}
                @if(session('error'))
                    <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-red-200">
                        <svg class="h-7 w-7 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span class="font-semibold text-lg">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Date d'envoi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Stagiaire</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Réunion</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($emailLogs as $log)
                                <tr class="hover:bg-indigo-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ optional($log->absence->stagiaire)->nom }} {{ optional($log->absence->stagiaire)->prenom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->to_email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ optional($log->absence->reunion)->date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($log->status === 'sent')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Envoyé</span>
                                        @elseif($log->status === 'failed')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" title="{{ $log->error_message }}">
                                                Échec
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-500 italic">
                                        Aucun email envoyé pour l'instant.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-end mt-6">
                    {{ $emailLogs->links('pagination::tailwind') }} {{-- Assurez-vous d'avoir la vue de pagination Tailwind --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
