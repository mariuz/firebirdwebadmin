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
                    <p class="text-muted">
                        <?= date('Y') ?>
                        -
                        <a href="https://github.com/mariuz/firebirdwebadmin">FirebirdWebAdmin</a>
                        <?php echo VERSION; ?>

                        <button type="button" class="btn btn-default btn-xs margin-left-10px" data-toggle="modal"
                                data-target="#userSettings">
                            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                            <?php echo $ptitle_strings["usr_cust"]; ?>
                        </button>

                        <a class="margin-left-10px" target="_blank" href="https://crowdin.com/project/firebirdwebadmin"><img src="https://d322cqt584bo4o.cloudfront.net/firebirdwebadmin/localized.svg"></a>
                    </p>
                </td>
                <?php
                if (DEBUG === true) {
                    echo "<td width=\"20%\">&nbsp;</td><td><div align=\"left\">\n";

                    show_time_consumption($start_time, microtime());

                    //     echo 'cookie size: '.strlen($_COOKIE[get_customize_cookie_name()])."<br>\n";
                    //     debug_var($_COOKIE[get_customize_cookie_name()]);

                    // display links to display the session, post or get variables
                    $session_url = url_session('./inc/display_variable.php?var=SESSION');
                    echo '<a href="' . $session_url . '" target="_blank">[ Session ]</a>' . "\n";

                    $post_url = url_session('./inc/display_variable.php?var=POST');
                    echo '<a href="' . $post_url . '" target="_blank">[ POST ]</a>' . "\n";

                    $get_url = url_session('./inc/display_variable.php?var=GET');
                    echo '<a href="' . $get_url . '" target="_blank">[ GET ]</a>' . "\n";

                    $kill_url = url_session('./inc/kill_session.php');
                    echo '<a href="' . $kill_url . '">[ kill session ]</a>' . "\n";

                    // Save the contents of $_POST and $_GET in session
                    $s_POST = $_POST;
                    $s_GET = $_GET;

                    echo "</div>\n</td>";
                }
                ?>
            </tr>
        </table>
    </div>

    <!-- User Settings Modal -->
    <div class="modal fade" id="userSettings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="settings.php" name="usr_cust_save"
                      class="form-horizontal">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo $ptitle_strings["usr_cust"]; ?></h4>
                    </div>
                    <div class="modal-body">

                        <?php if (empty($_COOKIE)):
                            echo '<div class="alert alert-danger" role="alert">' . $MESSAGES['COOKIES_NEEDED'] . "</div>";
                        else: ?>
                        <div class="form-group margin-left-10px">
                            <h4><?php echo $usr_strings['Appearance']; ?></h4>
                        </div>
                        <div class="form-group margin-left-10px">
                            <label for="usr_cust_language"
                                   class="col-sm-4 control-label"><?php echo $usr_strings['Language']; ?></label>

                            <div class="col-sm-5">
                                <?php echo get_selectlist('usr_cust_language', get_customize_languages(), $s_cust['language']); ?>
                            </div>
                        </div>
                        <div class="form-group margin-left-10px">
                            <h4><?php echo $usr_strings['Attitude']; ?></h4>
                        </div>
                        <div class="form-group margin-left-10px">
                            <label for="usr_cust_askdel"
                                   class="col-sm-4 control-label"><?php echo $usr_strings['AskDel']; ?></label>

                            <div class="col-sm-5">
                                <?php echo get_selectlist('usr_cust_askdel', array($usr_strings['Yes'], $usr_strings['No']), ($s_cust['askdel'] == 1 ? $usr_strings['Yes'] : $usr_strings['No'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" name="usr_cust_save"
                               value="<?php echo $button_strings['Save']; ?>">
                        <input type="submit" class="btn btn-default" name="usr_cust_defaults"
                               value="<?php echo $button_strings['Defaults']; ?>">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo $ptitle_strings["Close"]; ?></button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</footer>
<!--Scripts-->
<script src="https://code.jquery.com/jquery.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>
<script src="./js/miscellaneous.js" type="text/javascript"></script>
<?= js_global_variables()
. js_xml_http_request_client()
. js_request_close_panel()
. $js_stack
?>
</body>
</html>
