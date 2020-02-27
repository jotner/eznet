</div>
</div>
</div>

<?php wp_footer(); ?>

<footer class="footer">
  <div class="footer-columns" style="text-align:center;">
    <div class="columns is-centered">
      <div class="column is-one-third">
        <?php if(is_active_sidebar('footer-1')){
                dynamic_sidebar('footer-1');
              } ?>
      </div>
      <div class="column is-one-third footer-menu">
        <?php if(is_active_sidebar('footer-2')){
                dynamic_sidebar('footer-2');
              } ?>
      </div>
      <div class="column is-one-third">
        <?php if(is_active_sidebar('footer-3')){
                dynamic_sidebar('footer-3');
              } ?></span>
      </div>
    </div>
  </div>
  <hr style="background-color: #ccc; height: 1px;">
  <div class="columns is-centered is-mobile">
    <span class="footer-copyright">
      © 2019-2020, Eznet AB
  </div>
</footer>

</body>

</html>
