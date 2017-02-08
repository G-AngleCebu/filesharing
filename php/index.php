<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>jQuery File Upload Example</title>
    <style>
      .bar {
        height: 18px;
        background: green;
    }
</style>
</head>
<body>
    <div id="progress">
        <div class="bar" style="width: 0%; transition: width 0.2s;"></div>
    </div>
    <form>
        <!-- input -->
        <input id="fileupload" type="file" name="files[]" data-url="upload.php" multiple style="height: 200px; background-color: #f2f2f2;">
        <input type="radio" name="singleFileUpload" value="false" id="separate" checked /><label for="separate">Separate</label>
        <input type="radio" name="singleFileUpload" value="true" id="single" /><label for="single">Single</label>

        <div id="files">
        </div>

        <div id="target">
        </div>
    </form>

    
    <!-- Mustache.js templates -->
    <template id="file-upload-progress">
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
    </template>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.js"></script>
    <script src="js/jquery.widget.js"></script>
    <script src="js/jquery.iframe-transport.js"></script>
    <script src="js/jquery-file.js"></script>
    <script src="js/upload.js"></script>
</body> 
</html>