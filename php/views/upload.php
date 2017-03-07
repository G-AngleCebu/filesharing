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
        <!-- <div id="progress">
            <div class="bar" v-bind:style="{width: allProgress + '%'}"></div>
        </div> -->

        <!-- nav bar -->
        <div class="ui borderless fixed menu">
            <div class="ui container">
                <!-- home link -->
                <a href="/" class="item">
                    <b>File Sharing</b>
                </a>

                <div class="right menu">
                    <div class="item">
                        <a class="ui basic button">
                        New upload
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- progress bar -->
        <!-- <div class="ui container fluid" style="padding-top: 100px;">
            <div id="all-progress" class="ui tiny teal progress">
                <div class="bar"></div>
            </div>
        </div> -->
        
        <main id="file-upload-container" class="ui padded container">
            <div class="ui segment" v-if="files.length > 0">
                <!-- file list for upload -->
                <div class="file-upload ui divided items">
                    <template v-for="(file, index) in files">
                        <div class="item">
                            <div class="ui tiny image">
                                <img class="preview" :src="file.previewImageSrc"/>
                            </div>
                            <div class="middle aligned content">
                                <div class="header">
                                    <b>{{ file.name }}</b>
                                </div>
                                <div class="meta">
                                    {{ humanFileSize(file.size) }}
                                </div>
                                <div class="description">
                                    <div :id="'progress[' + file.key + ']'" name="prog" class="ui teal small progress" :data-percent="file.progress">
                                        <div class="bar">
                                            <div class="progress"></div>
                                        </div>
                                    </div>
                                    <!-- {{ file.progress }} / 100% -->
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- file list for completed files -->
            <div class="file-group" v-for="(uploadGroup, groupIndex) in uploadGroups">
                <div class="ui hidden divider"></div>

                <div class="ui borderless top attached menu">
                    <div class="item">
                        <div class="ui left icon action input">
                            <i class="lock icon"></i>
                            <input type="text" v-model.lazy="uploadGroup.password" placeholder="Password">
                            <button class="ui basic small button" v-on:click.prevent="generatePassword(groupIndex)">
                                Generate
                            </button>
                            <button class="ui small button" v-on:click.prevent="setPassword(groupIndex)">
                                Set password
                            </button>
                        </div>
                    </div>
                    <div class="right menu">
                        <div class="item">
                            <!-- share link form -->
                            <div class="ui action labeled input">
                                <input readonly type="text" :value="'<?php echo $baseUrl ?>' + uploadGroup.download_uid">
                                <button class="ui small icon button">
                                    <i v-on:click="copyToClipboard()" class="copy icon"></i>
                                </button>
                                <button class="ui small right labeled icon teal button" v-on:click.prevent="shareEmail(groupIndex)">
                                    <i class="share icon"></i>
                                    Share URL by email
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ui bottom attached segment">
                <!-- </div> -->
                <!-- <div class="ui bottom attached segment"> -->
                    
                    <!-- Files list -->
                    <div class="file-upload ui divided items">
                        <!-- Files LIST COMPACT VIEW -->
                        <table class="ui very basic table">
                            <!-- <thead>
                                <tr>
                                    <th colspan="2">File</th>
                                    <th>File size</th>
                                    <th>Date created</th>
                                </tr>
                            </thead> -->
                            <tbody>
                                <template v-for="(file, fileIndex) in uploadGroup.upload_files">
                                    <tr>
                                        <td class="one wide"><img class="ui fluid image" :src="file.previewImageSrc"/></td>
                                        <td>{{ file.file_name }}</td>
                                        <td>{{ humanFileSize(file.file_size) }}</td>
                                        <td>{{ humanDateTime(file.created_at) }}</td>
                                        
                                        <td>
                                            <button class="ui right floated tiny icon button basic" v-on:click.prevent="deleteFile(groupIndex, fileIndex, file.id)">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- bigger view -->
                        <!-- <template v-for="(file, fileIndex) in uploadGroup.upload_files">
                            <div class="item">
                                <div class="ui tiny image">
                                    <img class="preview" :src="file.previewImageSrc"/>
                                </div>
                                <div class="content">
                                    <button class="ui right floated tiny icon button basic" v-on:click.prevent="deleteFile(groupIndex, fileIndex, file.id)">
                                        <i class="remove icon"></i>
                                        Remove
                                    </button>
                                    <div class="header">
                                        {{ file.file_name }}
                                    </div>
                                    <div class="meta">
                                        {{ humanFileSize(file.file_size) }}<br/>
                                    </div>
                                </div>
                            </div>
                        </template> -->
                    </div>
                </div>
            </div>

            <!-- drag and drop div, upload file input -->
            <div id="file-input" class="ui segment middle aligned">
            <!-- v-if="showFileInput"> -->
                <div class="ui center aligned middle aligned grid">
                    <div class="column">
                        <!-- header -->
                        <div>
                            <h1 class="ui disabled icon header">
                                <i class="massive upload grey cloud icon"></i>
                                <div class="content">
                                    Drag & drop your files here
                                    <div class="sub header">
                                        or
                                    </div>
                                </div>
                            </h1>
                            <div>
                                <button v-on:click.prevent="browseFiles" id="browse-files" class="ui button">Browse files</button>
                            </div>
                        </div>
                        <div class="ui divider hidden"></div>
                        <form>
                            <!-- input -->
                            <input id="fileupload" type="file" hidden="true" name="files[]" data-url="api/upload" multiple>

                            <div id="singleFileUpload" class="ui slider checkbox">
                                <input id="singleFileUploadCheckbox" type="checkbox" v-model="singleFileUpload" name="singleFileUpload">
                                <label for="singleFileUploadCheckbox">Use separate URLs for each file</label>
                            </div>

                            <!-- <input v-model="singleFileUpload" type="radio" name="singleFileUpload" value="false" id="single" /><label for="single">Single</label>
                            <input v-model="singleFileUpload" type="radio" name="singleFileUpload" value="true" id="separate" /><label for="separate">Separate URL</label> -->
                        </form>
                    </div>
                </div>
            </div>
            <!-- end drag and drop div -->
        </main>

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
                    <div class="ui teal large approve fluid button">Send</div>
                </div>
            </div>
        </div>

        <div class="ui hidden divider"></div>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/moment.js"></script>
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