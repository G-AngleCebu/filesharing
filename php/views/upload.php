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
                        <img class="preview" :src="file.previewImageSrc"/>
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
                            <img class="preview" :src="file.previewImageSrc"/>
                            {{ humanFileSize(file.file_size) }}<br/>
                            <button v-on:click.prevent="deleteFile(groupIndex, fileIndex, file.id)">Delete</button>
                        </template>
                    </div>
                </div>
            </div>
        </form>

        <img src="" id="test"/>

        <div class="ui modal">
            <i class="close icon"></i>
            <div class="header">
                Share URL by E-mail
            </div>
            <div class="image content">
                <div class="ui container">
                    <form class="ui form twelve wide">
                        <div class="field">
                            <label>From</label>
                            <input v-model="email_from" name="from" type="text" placeholder="Your name"/>
                        </div>
                        <div class="field">
                            <label>To (separate with commas)</label>
                            <input v-model="email_to" name="to" type="email" placeholder="john@example.com, jane@email.com" multiple />
                        </div>
                        <div class="field">
                            <label>Cc (separate with commas)</label>
                            <input v-model="email_cc" name="cc" type="email" multiple />
                        </div>
                        <div class="field">
                            <label>Bcc (separate with commas)</label>
                            <input v-model="email_bcc" name="bcc" type="email" multiple />
                        </div>
                        <div class="field">
                            <label>Message</label>
                            <textarea v-model="email_message"></textarea>
                        </div>
                        <div class="field">
                            <div id="separate" class="ui checked checkbox">
                                <input type="checkbox" name="separate">
                                <label>Send the password in a separate e-mail</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="actions">
                <div class="container">
                    <div class="ui primary large approve fluid button">Send</div>
                </div>
            </div>
        </div>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/semantic.min.js"></script>
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