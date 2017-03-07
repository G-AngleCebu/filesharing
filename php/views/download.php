<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>File Sharing</title>
    <link rel="stylesheet" href="css/semantic.min.css">
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .ui.borderless.menu .item:last-child {
            padding-right: 0px;
        }

        #view-setting {
            text-align: right;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div id="app" v-cloak>
        <!-- nav bar -->
        <div class="ui borderless fixed menu">
            <div class="ui container">
                <!-- home link -->
                <a href="/" class="item">
                    <b>File Sharing</b>
                </a>

                <!-- download zip button -->
                <div class="right menu">
                    <div class="item">
                        <a :href="'/download/group/' + uploadGroup.id" class="ui teal button">
                            <i class="icon download"></i>
                            Download all as .zip
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- files list -->
        <main id="files-list" class="ui padded container">

            <!-- view settings -->
            <div id="view-setting">
                <div class="ui basic buttons">
                    <button class="ui icon button">
                        <i class="block layout icon"></i>
                    </button>
                    <button class="ui icon button active">
                        <i class="list layout icon"></i>
                    </button>
                </div>
            </div>

            <!-- files tables -->
            <div id="files">
                <!-- Files LIST COMPACT VIEW -->
                <table class="ui very basic table">
                    <thead>
                        <tr>
                            <th colspan="2">File</th>
                            <th>File size</th>
                            <th>Date created</th>
                            <th class="one wide"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(file, fileIndex) in uploadGroup.upload_files">
                            <tr>
                                <td class="one wide"><img class="ui fluid image" :src="file.previewImageSrc"/></td>
                                <td>{{ file.file_name }}</td>
                                <td>{{ humanFileSize(file.file_size) }}</td>
                                <td>{{ humanDateTime(file.created_at) }}</td>
                                <td><a :href="'/download/file/' + file.id" class="ui basic button">Download</a></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Files LIST BIG VIEW -->
                <!-- <div class="file-upload ui divided items">
                    <template v-for="(file, fileIndex) in uploadGroup.upload_files">
                        <form class="item" :action="'/download/file/' + file.id" method="GET">
                            <div class="image">
                                <img class="ui image" :src="file.previewImageSrc"/>
                            </div>
                            <div class="content">
                                <a class="header">{{ file.file_name }}</a>
                                <div class="meta">
                                    <span>{{ humanFileSize(file.file_size) }}</span>
                                </div>
                                <div class="description">
                                    <p>{{ file.created_at }}</p>
                                </div>
                                <div class="meta">
                                    <button class="ui button">Download</button>
                                </div>
                            </div>
                        </form>
                    </template>
                </div> -->
            </div>
        </main>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/semantic.min.js"></script>
    <script>
        var downloadUid = "<?php echo $uid; ?>";
        var password = "<?php echo $p; ?>";

        <?php if(!empty($error)): ?>
            alert("<?php echo $error; ?>");
        <?php endif; ?>
    </script>
    <script src="js/download.js"></script>
</body> 
</html>