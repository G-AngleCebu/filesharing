<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>jQuery File Upload Example</title>
    <style>
        .bar {
            height: 18px;
            background: green;
            transition: 0.2s;
        }
        [v-cloak] {
            display:none;
        }
        .file-upload {
            border: 1px solid #bbb;
            min-height: 50px;
            padding: 20px;
            margin-bottom: 10px;
        }
        .file-group {
            padding: 20px;
            margin-bottom: 10px;
            border: 3px solid black;
        }
    </style>
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
</head>
<body>
    <div id="app" v-cloak>
        <div id="progress">
            <div class="bar" v-bind:style="{width: allProgress + '%'}"></div>
        </div>
        <form>
            <!-- input -->
            <input id="fileupload" type="file" name="files[]" data-url="upload.php" multiple style="height: 200px; background-color: #f2f2f2;">
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
                <div class="file-group" v-for="(uploadGroup, index) in uploadGroups">
                    <h3>Group {{ index }}</h3>
                    <input v-model.lazy="uploadGroup.password" type="text" placeholder="Password" />
                    <button v-on:click.prevent="setPassword(index)">Set</button>
                    <button v-on:click.prevent="generatePassword(index)">Generate password</button>
                    
                    <div class="file-upload" v-for="(file, index) in uploadGroup.upload_files">
                        <template>
                            <b>[{{ file.id }}] {{ file.file_name }}</b><br/>
                            {{ humanFileSize(file.file_size) }}<br/>
                            <button v-on:click.prevent="deleteFile(index, file.id)">Delete</button>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Mustache.js templates -->
    <!-- <template id="file-upload-progress">
        <div class="file-upload" id="{{ slug }}">
            <b>{{ file.name }}</b><br/>
            {{ file.size }} bytes<br/>
            {{ progress }} / 100%
        </div>
    </template>

    <template id="file-upload">
        <div class="file-upload" id="{{ slug }}">
            <b>{{ file.name }}</b><br/>
            {{ file.size }} bytes<br/>
            Password: <input type="text" /><button>Set</button><button>Generate</button><br/>
            <button>Share</button>
        </div>
    </template> -->

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.js"></script> -->
    <script src="js/jquery.widget.js"></script>
    <script src="js/jquery.iframe-transport.js"></script>
    <script src="js/jquery-file.js"></script>
    <!-- <script src="js/upload.js"></script> -->
    <script>
        var downloadUid = '';
        <?php if(isset($_GET['id'])): ?>
        downloadUid = <?php echo $_GET['id']; ?>;
        <?php endif; ?>
    </script>
    <script src="js/app.js"></script>
</body> 
</html>