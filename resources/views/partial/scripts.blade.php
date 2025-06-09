<!-- ================== BEGIN BASE JS (De votre nouveau template) ================== -->
<!-- Ajoutez type="module" à ces balises script -->
<script src="/assets/js/vendor.min.js" type="module"></script>
<script src="/assets/js/app.min.js" type="module"></script>
<!-- ================== END BASE JS ================== -->

{{-- C'est ici que les scripts spécifiques aux vues (utilisant @push('scripts') ou @push('js')) seront injectés --}}
@stack('js') 
{{-- Si certaines de vos anciennes vues utilisaient @stack('scripts'), vous devrez changer en @push('js') dans ces vues. --}}
