<footer id="footer-bar"
        class="row <?= isset($this->params['layout']) && $this->params['layout'] == 'messages' ? 'hidden-md hidden-lg' : '' ?>">
    <p id="footer-copyright" class="col-xs-12">
        &copy; <?= date('Y'); ?> <a href="http://www.bigdropinc.com/" target="_blank">Bigdropinc</a>. Powered by
        BIGDROP.
    </p>
</footer>