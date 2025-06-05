<!-- ================== BEGIN BASE JS (De votre nouveau template) ================== -->
<script src="/assets/js/vendor.min.js"></script>
<script src="/assets/js/app.min.js"></script>
<!-- ================== END BASE JS ================== -->

{{-- C'est ici que les scripts spécifiques aux vues (utilisant @push('scripts') ou @push('js')) seront injectés --}}
@stack('js') {{-- Votre template utilise déjà @stack('js'), donc nous le gardons. --}}
{{-- Si certaines de vos anciennes vues utilisaient @stack('scripts'), vous devrez changer en @push('js') dans ces vues. --}}