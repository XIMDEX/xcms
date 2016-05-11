<app>
    <br/><br/><br/>
    <form name="postForm" method="post" action="{urlAction}" enctype="multipart/form-data" spellcheck="false">
        <div name="loading" class="spinner hidden" aria-hidden="true"></div>
        <input name="publish" type="hidden" value="{publish}"/>
        <div class="row">
            <div class="col-xs-9 borderright">
                <div class="row">
                    <div class="col-xs-9">
                        <div class="form-group">
                            <label for="title">Título del post:</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Titulo" value="{opts.titlep}">
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label for="fecha">Fecha:</label>
                            <input type="text" class="form-control" id="fecha" name="date" placeholder="Fecha" value="{opts.date}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <input name="intro" type="hidden" value="{intro}" />
                            <label for="intro">Entradilla:</label>
                            <div data-xim-editable="intro" id="intro">
                                <p data-ce-tag="intro"><yield from="intro"></yield></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="form-group">
                    <input type="hidden" name="imagexid" value="{imagexid}"/>
                    <span class="btn-file" title="Pulsa para seleccionar una imagen">
                        <input type="hidden" name="image" id="image" value="{imagePostUrl}" />
                        <label for="imagefile">
                            <div class="col-xs-12">
                                <span if={imagePostUrl.length == 0} class="btn btn-default">
                                    Seleccionar imagen
                                </span>
                                <div if={imagePostUrl.length > 0} class="container-preview">
                                    <img class="img-responsive img-responsive-max img-thumbnail" src="{imagePostUrl}" id="imageview" />
                                </div>
                            </div>
                        </label>
                        <input type="file" name="imagefile" id="imagefile" accept="image/*" />
                    </span>
                    <br/>
                    <input id="left" name="left" type="hidden" value="{left}" />
                    <input id="top" name="top" type="hidden" value="{top}" />
                    <input id="width" name="width" type="hidden" value="{width}" />
                    <input id="height" name="height" type="hidden" value="{height}" />
                    <input id="rotate" name="rotate" type="hidden" value="{rotate}" />
                </div>
            </div>
        </div>
        
        
        
        <div class="form-group bordertop">
            <label>Contenido:</label>
            <input name="content" type="hidden" value="{content}" />
            <editor>
            <yield from="content"></yield>
            </editor>
        </div>
    </form>

    <div id="bottombar">
        {status}
    </div>

    <script>
        var that = this;
        that.urlAction = window.location.href + "/save";
        that.imagePostUrl = opts.image;
        that.left = "";
        that.top = "";
        that.width = "";
        that.height = "";
        that.intro = "";
        that.content = "";
        that.status = "";
        that.publish = "false";
        that.cropDialog = null;
        that.mode = that.opts.mode;
        that.imagexid = opts.imagexid
        this.on('mount',function(){
            moment.locale('es');
	    var myDate;
            if(that.opts.date){
	      myDate = that.opts.date;	
	    } else {
	      myDate = new Date();
	    }
            rome(that.fecha,{
                'time': false,
                "inputFormat": "DD/MM/YYYY",
		"initialValue": myDate,
            });
            $("#imagefile").change(function(){
                if (this.files && this.files[0]) {
                    $(that.loading).removeClass('hidden');
                    var reader = new FileReader();
                    var t = this;
                    reader.onload = function (e) {
                        that.showCropDialog(e.target.result, t.files[0]);
                        $(t).val("");
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });
            $(document).ajaxStart(function(){
                $(that.loading).removeClass('hidden');
            });
            $(document).ajaxStop(function(){
                $(that.loading).addClass('hidden');

                //that.loading = false;
                //that.update();
            });
        });

        that.submitForm = function(callback){
            that.update();

            var formData = new FormData(that.postForm);
            
            $.ajax({
                url: that.urlAction,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if(data.redirect && inIframe()){
                        if(data.reloadnode){
                            var s = window.parent.$('#angular-tree').isolateScope()
                            s.reloadNode(data.reloadnode);
                        }
                        window.onbeforeunload = function () {};
                        $(that.loading).addClass('hidden');
                        new ContentTools.FlashUI('ok');
                        setTimeout(function(){
                            window.location.href = location.protocol + '//' + location.host + data.redirect;
                        }, 600);
                        return;
                    }
                    callback(data);
                },
                error: function(){
                    new (ContentTools.FlashUI)('no');
                },
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json"
            });
            

        }
        that.showCropDialog = function(data, file){

            if(that.cropDialog != null) {
                return;
            }

            var app, dialog, modal;

            app = ContentTools.EditorApp.get();

            modal = new ContentTools.ModalUI();

            that.dialog = new ContentTools.CropImageDialog(data, file);

            that.dialog.bind('cancel', (function(_this) {
                return function() {
                    that.dialog.unbind('cancel');
                    modal.hide();
                    that.dialog.hide();
                    that.dialog = null;
                };
            })(this));

            that.dialog.bind('save', (function(_this) {
                return function(url, id) {
                    that.imagePostUrl = url;
                    that.imagexid = id;
                    that.update();
                    that.dialog.unbind('cancel');
                    modal.hide();
                    that.dialog.hide();
                    that.dialog = null;
                };
            })(this));

            that.dialog.bind('CropImageDialog.mount', (function(_this) {
                return function() {
                    setTimeout(function(){
                        $(that.loading).addClass('hidden');
                    }, 200);
                };
            })(this));

            app.attach(modal);

            app.attach(that.dialog);

            modal.show();

            that.dialog.show();
        }
    function inIframe () {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    }
    </script>

    <style scoped>
        div[data-xim-editable]{
            background-color: rgba(255,255,255,.7);
            padding: 5px 10px;
        }
        #bottombar {
            position: fixed;
            z-index: 100; 
            bottom: 0; 
            left: 0;
            width: 100%;
            height: 20px;
            color: #ffffff;
            font-family: "Coustard";
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#72c4b9), to(#7ccac0));
            background-image: -webkit-linear-gradient(#72c4b9, #7ccac0);
            background-image: -moz-linear-gradient(#72c4b9, #7ccac0);
            background-image: -o-linear-gradient(#72c4b9, #7ccac0);
            background-image: linear-gradient(#72c4b9, #7ccac0);
            font-size: 14px;
            text-shadow: 0 -1px 0 #60b6aa,0 1px 0 rgba(255,255,255,.2);
            border-left: 5px solid #e3e7e9;
            border-right: 5px solid #e3e7e9;
            border-bottom: 2px solid #e3e7e9;
            padding-left: 5px;
            padding-top: 1px;
        }
        form {
            padding-bottom: 25px;
        }

        .btn-file input[type=file] {
            display: none;
        }
        .container-preview{
            width: 145px;
            margin: 0 auto;
        }
        #previewimage {
	    width: 120px;
            height: 160px;
            overflow: hidden;
        }

        .form-group{
            margin-bottom: .5em;
        }
        @keyframes anim-rotate {
            0% {
                transform: rotate(0);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        .spinner::before{
            content: '\e97c';
        }
        .spinner {
	    z-index: 99;
            position: fixed;
            left: calc(50% - .5em);
            top: calc(50% - 2.5em);
            font-family: 'icon';
            display: inline-block;
            font-size: 22em;
            height: 5em;
            line-height: 5;
            color: #fff;
            text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
            animation: anim-rotate 1s infinite steps(12), zoomIn 1s 1;
        }
        @-webkit-keyframes zoomIn {
          from {
            opacity: 0;
            -webkit-transform: scale3d(.3, .3, .3);
            transform: scale3d(.3, .3, .3);
          }

          50% {
            opacity: 1;
          }
        }

        @keyframes zoomIn {
          from {
            opacity: 0;
            -webkit-transform: scale3d(.3, .3, .3);
            transform: scale3d(.3, .3, .3);
          }

          50% {
            opacity: 1;
          }
        }
    .img-thumbnail{
        display: inline-block;
        max-width: 100%;
        height: auto;
        padding: 4px;
        background-color: #fff;
        border: 1px solid #CDCDCD;
        border-radius: 4px;
    }
    </style>
</app>
