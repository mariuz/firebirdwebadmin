<?php
/**
 * Created by PhpStorm.
 * User: gilcierweb
 * Date: 14/05/15
 * Time: 08:26
 */
?>
            <footer class="text-center">
                <hr />
                <p><?=date('Y')?> - FirebirdWebAdmin 3.0</p>
            </footer>
        </div>
    </div>
</div>
<!--Scripts-->
<script src="https://code.jquery.com/jquery.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.js"></script>
<script src="./js/miscellaneous.js" type="text/javascript"></script>
<?=  js_global_variables()
. js_xml_http_request_client()
. js_request_close_panel()
. $js_stack
?>
</body>
</html>