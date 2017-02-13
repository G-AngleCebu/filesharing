<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>File Sharing</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
</head>
<body>
    <div id="app" v-cloak>
        <div id="progress">
            <div class="bar" v-bind:style="{width: allProgress + '%'}"></div>
        </div>
        <form>
            <!-- input -->
            <input id="fileupload" type="file" name="files[]" data-url="api/upload" multiple>
            <input v-model="singleFileUpload" type="radio" name="singleFileUpload" value="false" id="single" /><label for="single">Single</label>
            <input v-model="singleFileUpload" type="radio" name="singleFileUpload" value="true" id="separate" /><label for="separate">Separate URL</label>

            <div id="files">
                <div class="file-upload" v-for="(file, index) in files">
                    <template>
                        <b>{{ file.name }}</b><br/>
                        {{ humanFileSize(file.size) }}<br/>
                        {{ file.progress }} / 100%
                    </template>
                </div>
                <div class="file-group" v-for="(uploadGroup, groupIndex) in uploadGroups">
                    <h3>Group {{ groupIndex }}</h3>
                    <h4>URL: <a :href="'/' + uploadGroup.download_uid">Share URL</a></h4>
                    <input v-model.lazy="uploadGroup.password" type="text" placeholder="Password" />
                    <button v-on:click.prevent="setPassword(groupIndex)">Set</button>
                    <button v-on:click.prevent="generatePassword(groupIndex)">Generate password</button>
                    <button v-on:click.prevent="shareEmail(groupIndex)">Share this URL by email</button>
                    
                    <!-- Files list -->
                    <div class="file-upload" v-for="(file, fileIndex) in uploadGroup.upload_files">
                        <template>
                            <b>[{{ file.id }}] {{ file.file_name }}</b><br/>
                            {{ humanFileSize(file.file_size) }}<br/>
                            <button v-on:click.prevent="deleteFile(groupIndex, fileIndex, file.id)">Delete</button>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.js"></script> -->
    <script src="js/jquery.widget.js"></script>
    <script src="js/jquery.iframe-transport.js"></script>
    <script src="js/jquery-file.js"></script>
    <!-- <script src="js/upload.js"></script> -->
    <script>
        var downloadUid = '';
        <?php if(isset($_GET['uid'])): ?>
            downloadUid = "<?php echo $_GET['uid']; ?>";
        <?php endif; ?>
    </script>
    <script src="js/app.js"></script>
</body> 
</html>