<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>File Sharing</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="css/semantic.min.css">
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <style>
        body > .grid {
            height: 100%;
        }
    </style>
</head>
<body class="gray">
    <div class="ui middle aligned grid">
            <div class="column">
                <div class="ui padded text container segment">
                    <form class="ui large form error" action="/<?php echo $uid; ?>" method="POST">
                        <h2 class="ui header">The file you are trying to access is password protected.</h2>

                        <!-- error message -->

                        <?php
                            if(isset($messages['error'])){
                                foreach($messages['error'] as $message){
                                    echo '<div class="ui error message">';
                                    echo '<div class="header">Authentication failed.</div>';
                                    echo "<p>{$message}</p>";
                                    echo '</div>';
                                }
                            }
                        ?>

                        <!-- password field -->
                        <div class="field">
                            <div class="ui left icon input">
                                <i class="lock icon"></i>
                                <input name="password" type="password" placeholder="Password" />
                            </div>
                        </div>
                        <button class="ui fluid large teal button" type="submit" />Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/semantic.min.js"></script>
</body> 
</html>