 <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6">
            <div class="footer-info">
              <h3 class="logo me-auto me-lg-0"><a href="index.php">Caba&ntilde;as Las Lengas</a></h3>
              <p>
                Gabriel Silva (Camino 1) 132. Puerto Natales.
                Regi&oacute;n del Magallanes<br/><br/>
                <strong>Fono:</strong> (+56) 9-93499820<br/>
                <strong>Email:</strong> reservaciones@cabanaslaslengas.cl<br>
              </p>
              <div class="social-links mt-3">
                <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
                <!--<a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
                <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>-->
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <!--<h4>Useful Links</h4>-->
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Nostros</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Serviciones</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Equipamiento</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
           <!-- <h4>Our Services</h4>-->
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Caba&ntilde;as</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Galer&iacute;a</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Contacto</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Resrvaciones</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4>Our Newsletter</h4>
            <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
            <form action="" method="post">
              <input type="email" name="email"><input type="submit" value="Subscribe">
            </form>

          </div>

        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
         &reg; <strong style="color:#A9AD00 ;"><span>Caba&ntilde;as Las Lengas</span></strong>. 2021
      </div>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/restaurantly-restaurant-template/--> 
        Puertos Natales. Patagonia de Chile.
      </div>
    </div>
  </footer><!-- End Footer -->

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  
  
  <script src="assets/cabanas/main.js"></script>
  

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <?php $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match("/mobile/i", $useragent) ) { ?>
        <a href="https://api.whatsapp.com/send?phone=56993499820&text=Hola, quisiera m&aacute;s informaci&oacute;n sobre las caba&ntilde;as" class="floatm1" target="_blank"><i class="bi bi-whatsapp my-float"></i></span></a>
        <a href="#" class="floatm2" ><span style="font-size: 15px;color: #fff !important;"><i class="bi bi-facebook my-float2"></i></span></a>
        <a href="#" class="floatm3"><span style="font-size: 15px;color: #fff !important;"><i class="bi bi-instagram my-float3"></i></i></span></a>
        
	<?php } else {  ?>       
        <a href="https://web.whatsapp.com/send?phone=56993499820&text=Hola, quisiera m&aacute;s informaci&oacute;n sobre las caba&ntilde;as" class="float" target="_blank" title="Consulte cualquier duda via whatsapp"><span  style=""><i class="bi bi-whatsapp my-float"></i> WhatsApp</span></a>
        <a href="#" class="float2" ><span style="" title="Facebook"><i class="bi bi-facebook my-float2"></i> Facebook </span></a>
        <a href="#" class="float3" ><span style="" title="Instagram"><i class="bi bi-instagram my-float3"></i> Instagram </span></a>
    <?php }   ?>