<?php
/**
 * Created by PhpStorm.
 * User: gilcierweb
 * Date: 14/05/15
 * Time: 08:26
 */
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
        </div>
    </div>
</div>
<footer class="footer">
	<div class="container">
		<table>
			<tr>
				<td>	
					<p class="text-muted"><?=date('Y')?> - <a href="https://github.com/mariuz/firebirdwebadmin">FirebirdWebAdmin</a> <?php echo VERSION; ?></p>
				</td>
<?php 
if (DEBUG === TRUE) {
    echo "<td width=\"30%\">&nbsp;</td><td><div align=\"left\">\n";

    show_time_consumption($start_time, microtime());

//     echo 'cookie size: '.strlen($_COOKIE[get_customize_cookie_name()])."<br>\n";
//     debug_var($_COOKIE[get_customize_cookie_name()]);

    // display links to display the session, post or get variables
    $session_url = url_session('./inc/display_variable.php?var=SESSION');
    echo '<a href="'.$session_url.'" target="_blank">[ Session ]</a>'."\n";

    $post_url = url_session('./inc/display_variable.php?var=POST');
    echo '<a href="'.$post_url.'" target="_blank">[ POST ]</a>'."\n";

    $get_url = url_session('./inc/display_variable.php?var=GET');
    echo '<a href="'.$get_url.'" target="_blank">[ GET ]</a>'."\n";

    $kill_url = url_session('./inc/kill_session.php');
    echo '<a href="'.$kill_url.'">[ kill session ]</a>'."\n";

    // Inhalt von $_POST und $_GET in der Session hinterlegen
    $s_POST = $_POST;
    $s_GET  = $_GET;

    echo "</div>\n</td>";
}
?>
			</tr>
		</table>
	</div>
</footer>
<!--Scripts-->
<script src="https://code.jquery.com/jquery.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>
<script src="./js/miscellaneous.js" type="text/javascript"></script>
<?=  js_global_variables()
. js_xml_http_request_client()
. js_request_close_panel()
. $js_stack
?>
</body>
</html>
