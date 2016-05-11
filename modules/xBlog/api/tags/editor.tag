<editor>
	<div data-xim-editable="content">
		<yield/>
	</div>


	<script>
		var that = this;

		var parent = that.parent;
		window.editor = null;

			this.on('mount',function(){
				if(window.editor == null){
					that.updateStatus();
					ContentTools.IMAGE_UPLOADER = ImageUploader;
					window.editor = ContentTools.EditorApp.get();
					window.editor.init('*[data-xim-editable]', 'data-xim-editable');

					window.editor.start();

					window.editor.bind('save', function(regions, autoSave) {
						var saved;
						window.editor.busy(true);

						var r = window.editor.regions()["content"];
						parent.content = r.html();
						var r2 = window.editor.regions()["intro"];
						parent.intro = r2.nextContent().content.html();
						parent.publish = "false";
						callback = function(){
							that.updateStatus();
							window.editor.busy(false);
							new ContentTools.FlashUI('ok');
							return true;
						};
						parent.submitForm(callback);
				  });

					window.editor.bind('publish', function(regions, autoSave) {
						var saved;
						window.editor.busy(true);

						var r = window.editor.regions()["content"];
						parent.content = r.html();
						var r2 = window.editor.regions()["intro"];
						parent.intro = r2.nextContent().content.html();
						parent.publish = "true";

						callback = function(){
							that.updateStatus();
							window.editor.busy(false);
							new ContentTools.FlashUI('ok');
							return true;
						};
						parent.submitForm(callback);


				  });

			}
		});

			that.urlInfo = window.location.href + "/info";

			that.updateStatus = function(){
				$.ajax({
					url: that.urlInfo,
					type: 'GET',
					dataType: "json",
					async: false,
					success: function (data) {
					parent.status = "Nombre: " + data.data.name;

                	parent.status += " | Ult. Modificación:  " + that.convertTimestamp(data.data.date);
                	
                	parent.update();
                	console.log(data);
                }
            });
			};

			that.convertTimestamp = function(timestamp) {
		  var d = new Date(timestamp * 1000),	// Convert the passed timestamp to milliseconds
		  yyyy = d.getFullYear(),
				mm = ('0' + (d.getMonth() + 1)).slice(-2),	// Months are zero based. Add leading 0.
				dd = ('0' + d.getDate()).slice(-2),			// Add leading 0.
				hh = d.getHours(),
				h = hh,
				min = ('0' + d.getMinutes()).slice(-2),		// Add leading 0.
				ampm = 'AM',
				time;

				if (hh > 12) {
					h = hh - 12;
					ampm = 'PM';
				} else if (hh === 12) {
					h = 12;
					ampm = 'PM';
				} else if (hh == 0) {
					h = 12;
				}

			// ie: 2013-02-18, 8:35 AM	
			time = yyyy + '-' + mm + '-' + dd + ', ' + h + ':' + min + ' ' + ampm;

			return time;
		}
		
	</script>

	<style scoped>
		div[data-xim-editable]{
			background-color: rgba(255,255,255,.7);
			padding: 5px 10px;
		}
	</style>
</editor>
