<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>File Sharing</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/semantic.min.css">
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
</head>
<body>
    <div id="app" v-cloak>
        <div id="files">
            <div class="file-group" v-for="(uploadGroup, groupIndex) in uploadGroups">
                <h3>Group {{ groupIndex }}</h3>
                <form :action="'/download/group/' + uploadGroup.id" method="GET">
                    <button>Download zip</button>
                </form>
                
                <!-- Files list -->
                <div class="file-upload" v-for="(file, fileIndex) in uploadGroup.upload_files">
                    <template>
                        <form :action="'/download/file/' + file.id" method="GET">

                            <b>{{ file.file_name }}</b><br/>
                            <img class="preview" :src="file.previewImageSrc"/>
                            {{ humanFileSize(file.file_size) }}<br/>
                            {{ file.created_at }}<br/>
                            <button>Download</button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
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