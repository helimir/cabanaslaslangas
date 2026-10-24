    </div> <!-- Cierre de main-content -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Función para toggle del sidebar
        $('#toggle-sidebar').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Para desktop: toggle collapsed
            if ($(window).width() > 992) {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            } else {
                // Para móvil: toggle show
                $('#sidebar').toggleClass('show');
            }
        });

        // Cerrar sidebar en móvil al hacer clic en un enlace
        $('.nav-link').on('click', function() {
            if ($(window).width() <= 992) {
                $('#sidebar').removeClass('show');
            }
        });

        // Asegurar que el sidebar mantenga su estado en desktop
        $(window).on('resize', function() {
            if ($(window).width() > 992) {
                $('#sidebar').removeClass('show');
                if ($('#sidebar').hasClass('collapsed')) {
                    $('#main-content').addClass('expanded');
                } else {
                    $('#main-content').removeClass('expanded');
                }
            } else {
                $('#main-content').removeClass('expanded');
            }
        });

        // Auto-cerrar alertas
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>
</html>